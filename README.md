### Core 
	#Estrutura de pastas
		
	legenda:	* matriz
				= pasta com pastas
				- pastas com arquivos
				+ pasta com pastas e arquivos
				[ entidades independentes
				// comentários
				| Arquivo
				o pasta vazia
				*-> novo
				>-> movido
				|-> remover
				
	
Explicações
.tnm são arquivos que interagem com o terminal
		----------------------------------------------------

    *->	*+ usr
			= tema
				= lliure
					- icone
						| *.png
					- img
						| *.png
					- explorador
						| explorador.css
						| app.css
						| explorador.php
						| janela.php
					
				= $tema		
					- icone
						// icones
						
					- img
						// imagens do tema
						
					- login
						| login.html
						| login.css
						| login.js
						
					- explorador
						| app.css
						| explorador.php
						| janela.php
				
			- explorador
				| exp.php
				| exp.js
				
			- css
				| padrao.css  // resets de css
				| terminal.css
				
			- js
				| jquery.js
				| lliure.js
				| terminal.js
				
			- lliure
				| ll.php
				| token.php
				| bd.php
    |-> 		| jf.fnc.php
				| terminal.php // le arquivos .ll 	
				
		*= opt
			+ stripanelo // painel de controle
    *->			| stripanelo.ll
				o instalilo
				- img
					| install.ico.png
					| menu_rapido.png
				| ne_trovi.php
				| menu-rapido.php
					
			- desktop
				| desktop.header.php
				| desktop.php
				| sen_html.php				
				
			- user
				| ajax.novo_usuario.php
				| usuario.header.php
				| usuarios.php
    |->		| loguser.php

			+ login	
				login.php 			
				- nli
					| start.php
					| onserver.php
					| onclient.php
    |->		| rotinas.php
			
    *->		+ instalador
				
		*= app
			//app's
			+ listiser
				- listiser.header.php
				- listiser.php		//trabalhará usando outputbuffer não tendo a necessidade do arquivo 
				- listiser.os.php
				- listiser.oc.php	// para trabalho em ajax
    *			- listiser.ll.php	// informações para rodar no lliure (terminal)
				- listiser.tmn.php
				- nli
					| start.php
					| onserver.php
					| onclient.php
					
		*= api
			[ jfbox
			[ aplimo
			[ appbar
		  	[ fileup
			[ fotos
			[ navigi
			[ phpmailer
			[ tiny_mce
			
		*+ etc
			//configs
			
    *->	*| boot.php
		*| .htaccess
    |->	*| acoes.php
    |->	*| index.php
    |->	*| kun_html.php
    |->	*| nli.php
    |->	*| onserver.php
    |->	*| sen_html.php

-----------------------------------------
	
	Ex. onserver: sistema/onserver/newsmade
	Ex. home: sistema/newsmade
	Ex. sen_html: sistema/onclient/newsmade
	
	Ex: sistema/nli/onserver/app=listizer?ac=add&email=teste@teste.com.br
	
	Ex: sistema/nli/onserver/listizer?ac=add&email=teste@teste.com.br
	
	Ex: sistema/listizer?ac=add&email=teste@teste.com.br
	
	Ex: sistema/terminal/?cmd="newsmade newpost"
	
	
<?php
	if( llcmd('newsmade newpost') ){
		echo 'post criado';
	}
?>
	
	
	(user newuser)
	
	nextStep: newuser2
	
	$('.new').click(function(){
		llcmd('user newuser', function(){
			//executa nextStep
		});
		
		return false;
	});
	
	
	
	
	
	
	$('.new').click(function(){
		llcmd('newsmade newpost', function(){
			navigi('load');
		});
		
		return false;
	});
		
	**********************************************************************************
	1: opt
	2: api
	3: app
	
	
	> users add rodrigo
	> api users 
	
	> listiser new 
	
	> listiser
	
	listizer -> new 
	listizer -> sair
	
	/*******************/
	> skynet rodrigoDechen -p 
	
	skynet -> digite sua senha: 
	
	skynet -> logado com sucesso!
	skynet -> exit
	
	# startx
	/*******************/
	
	
	skynet - ssk: aksjdfhalkdjfhasdlkfjsahdlfkjh
	site < 1234
	
	Dev - xpto
	
	
	
	***********************************