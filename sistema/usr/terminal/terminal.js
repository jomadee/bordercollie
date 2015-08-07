if(typeof ll == 'undefined')
	ll = {};

ll.terminal = function(comando, callback){
	
	var self = ll.terminal;

	if(self.prefixo.length > 0 && comando.length > 0)
		comando = '"' + comando.replace(/\\/g, '\\\\').replace(/"/g, '\\"') + '"';

	$.getJSON('terminal', {cmd: self.prefixo + comando}, function(data){
		self.prefixo = '';
		self.core.before(data);
		
		if(typeof callback[arrayKeys(data)[0]] !== 'undefined')
			callback[arrayKeys(data)[0]](data);
		
		else if(typeof self.core[arrayKeys(data)[0]] !== 'undefined')
			self.core[arrayKeys(data)[0]](data, callback);
		
		else
			self.core.string(data, callback);
		
		self.core.after(data);
		
	}).fail(function(data){
		self.core.string({'string': data.responseText}, callback);
	});
	
	function arrayKeys(input){
		var output = new Array();
		var counter = 0;
		for (var i in input) {
			output[counter++] = i;
		} 
		return output; 
	}
	
};

ll.terminal.prefixo = '';
	
ll.terminal.ex = function(name, callback){
	ll.terminal.core[name] = callback;
};

ll.terminal.core = {};

ll.terminal.core.before = function(data){};

ll.terminal.core.after = function(data){};

ll.terminal.core.string = function(data, callback){
	if(callback) callback(data.string);
};

ll.terminal.core.object = function(data, callback){
	if(callback) callback(data);
};

ll.terminal.core.error = function(data, callback){
	if(callback) callback(data.error);
};

ll.terminal.core.read = function(data, callback){
	if(typeof data.prefixo != 'undefined')
		ll.terminal.prefixo = data.prefixo;
	if(callback) callback(data.read);
};