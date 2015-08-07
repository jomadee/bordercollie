ll.terminal.core.obj = function(data, callback){
	var r = '';
	
	for(var i in data.obj){
		
		r += '' + i + ': '+ data.obj[i]+ "\n";
		
	};
	
	if(callback) callback(r);

};

(function(){

	var

	commandsQueue = new Array(),
	currentCommand = 0,
	
	isPassword = false,
	
	user = {
		'logon': false,
		'name': 'convidado'
	},
	
	responder = '',
	resposta = null,
	terminal = $('#terminal'),
	input = null,
	texto = null,
	linha = null;

	ll.terminal.core.after = function(data){
		if(typeof data.responder !== 'undefined')
			responder = data.responder;
	};

	ll.terminal.ex('password', function(data){
		ll.terminal.prefixo = data.prefixo? data.prefixo: '';
		resposta.remove();
		nextline(data.password, true);
	});

	ll.terminal.ex('read', function(data){
		ll.terminal.prefixo = data.prefixo? data.prefixo: ''; 
		resposta.remove();
		nextline(data.read);
	});


	this.nextline = function(text, password){
		
		if((typeof text === 'boolean') && (typeof password === 'undefined')){
			password = text;
			text = undefined;
		}
		
		var newLine = $(
			'<pre>'+
				'<span class="text">'+ ((typeof text !== 'undefined')? text: ((user.logon? user.name+ '&nbsp;': '') + '>&nbsp;')) + '</span>'+
				'<span class="input'+ (password? ' password': '')+ '" contentEditable="true"></span>'+
				'&nbsp;'+
			'</pre>'
		);
		
		terminal.append(newLine);
		
		input = $('.input', newLine);
		texto = $('.text', newLine);
		linha = newLine;
		
		input.focus();
		
		//console.log(input, texto, linha);
	};
	
	this.desativandoLinha = function(){
		$(input)
			.removeClass('input')
			.prop('contentEditable', false);
		
		input = null;
		texto = null;
		linha = null;
	};

	terminal.on('keydown', '.input', function(e){
		
		if( e.which == 9
			|| e.which == 38
			|| e.which == 40)
			e.preventDefault();

		/* return - 13 */
		if(e.which == 13){
			
			//console.log(input.html());
			
			if(ll.terminal.prefixo.length > 0 || input.html().length > 0){
				
				isPassword = input.hasClass('password');
				var cmd = input.html();
				var cmdp = !isPassword? cmd: "*".repeat(cmd.length);
				
				desativandoLinha();
				
				if(!isPassword && commandsQueue[commandsQueue.length - 1] != cmdp){
					commandsQueue[commandsQueue.length] = cmdp;
					currentCommand = commandsQueue.length;
					if(currentCommand == 51){
						currentCommand = 50;
						commandsQueue.slice(1, 51);
					}
				}
				
				currentCommand = (commandsQueue.length);
				
				resposta = $('<pre>loading...\n\r</pre>');
				terminal.append(resposta);

				ll.terminal(cmd, function(data){
					resposta.html(data + "\n\r");
					nextline();
				});
			}
			
			e.preventDefault();
			return false;
			
		}
		
	});
	
	terminal.on('keyup', '.input', function(e){

		/* tab - 9 */
//		if(e.which == 9 && !isRunningCommand){
//			if(searchAttempts == 0)
//				searchSubject = input.val();
//			$.post('core/comsearch.php',
//				{filename: searchSubject ,attempts: searchAttempts},
//				function(data){
//					if(data.length > 0)
//						input.val(data);
//					else
//						searchAttempts = -1;
//				});
//			searchAttempts++;
//		}else{
//			searchAttempts = 0;
//		}

		/* up - 38 */
		if(e.which == 38){
			if((currentCommand) >= 0){
				if(currentCommand != 0)currentCommand--;
				input.html(commandsQueue[currentCommand]);
			}
			//console.log(currentCommand, commandsQueue.length);
		}

		/* down - 40 */
		if(e.which == 40){
			if((currentCommand) <= commandsQueue.length - 1){
				if((currentCommand) < commandsQueue.length - 1){
					currentCommand++;
					input.html(commandsQueue[currentCommand]);
				}else{
					input.html('');
				}
			}
			//console.log(currentCommand, commandsQueue.length);
		}
		
	});
	
	nextline();

})();