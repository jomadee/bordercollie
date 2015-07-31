<?php


if(version_compare(phpversion(), '5', '<'))
	die('Seu servidor PHP está na versão '. phpversion(). ' para o funcionamento pleno do sistema é exigido a versão 5');


/** startando a session */
session_start('lliure');


/** definições basicas do sistema */
define('DS', DIRECTORY_SEPARATOR);
define('BASE_PATH', realpath(dirname(__FILE__)));


/** Processamento e configuração da url */
$file = explode('/', $_SERVER['PHP_SELF']); $file = array_pop($file);
$bptw = substr($_SERVER['PHP_SELF'], 0, -(strlen($file) + 1));
$qyst = ((!empty($_SERVER['QUERY_STRING'])? '?': ''). $_SERVER['QUERY_STRING']);
$rdtu = str_replace($qyst, '', $_SERVER['REQUEST_URI']);
$bsgt = trim(str_replace($bptw, '', $rdtu), "/");
$_ll['url']['home'] = (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on' ? 'https' : 'http'). '://'. $_SERVER['HTTP_HOST']. $bptw. '/';
$_ll['url']['path'] = $bsgt;
$_ll['url']['base'] = $_ll['url']['home']. $_ll['url']['path'];
$_ll['url']['full'] = $_ll['url']['base']. $qyst;
$_ll['url']['get'] = (!empty($bsgt)? explode('/', $bsgt): array());
unset($file, $bptw, $qyst, $rdtu, $bsgt);


/** Carregando e configunado o Autoload */
require_once BASE_PATH. DS. 'usr'. DS. 'autoload'. DS. 'autoload.php';
autoload::setPath(BASE_PATH. DS. 'usr');
autoload::setPath(BASE_PATH. DS. 'api');
autoload::setPath(BASE_PATH. DS. 'app');


/** Verifica a existemcia do inicialozador (confg.php) */
if(!file_exists(($f = BASE_PATH. DS. 'etc'. DS. 'confg.ll')))
    die('file: '. $f. '; não existemte.');


/** Instacia as configurações definidas no arquivo confg */
else $_ll = require_once $f;

/** retorna os dados processados/carregados pelo start */
if((str_replace('/', DS, (trim($_SERVER['DOCUMENT_ROOT'], DS). $_SERVER['SCRIPT_NAME']))) !== (trim(str_replace('/', DS, __FILE__), DS)))
    return $_ll;







$_ll['operation_types'] = array('opt', 'api', 'app');
$_ll['operation_mode'] = 'start';
$_ll['operation_type'] = false;
$_ll['enter_mode'] = 'start';
$_ll['terminal'] = false;
$arrURL = $_ll['url']['get'];


/** Verifica se a requsição é para o terminal; */
if(($chave = array_search('terminal', $arrURL)) !== false){
	$_ll['terminal'] = true;
	unset($arrURL[$chave]);
}


/** Verifica se a pessoa esta logada; */
if(($chave = array_search('nli', $arrURL)) !== false || !lliure::get_login()){
	$_ll['enter_mode'] = 'nli';
	if($chave != false) 
		unset($arrURL[$chave]);
}


/** Verifica se é para o terminal */
if($_ll['terminal']){
	
	/** Se existri um comando */
	if(isset($_GET['cmd'])){
		/** Executa o terminal */
		terminal::start($_GET['cmd']); die();
	
	/** se nao existir um comando */
	}else{
		/** Seta a parte visial do terminal */
		$_ll['terminal'] = FALSE;
		$_ll['operation_type'] = 'opt';
		$_ll['operation_load'] = 'terminal';
	}
}


/*echo
    '<pre>'. print_r($_SERVER, true). '</pre>'.
    '<pre>'. print_r($_SESSION, true). '</pre>'.
    '<pre>'. print_r($_ll, true). '</pre>',
    '<pre>'. print_r($arrURL, true). '</pre>';*/


/** define o modo de operação do sistema [onserver, onclient]; */	
if(($chave = array_search('onclient', $arrURL)) !== false){
	$_ll['operation_mode'] = 'onclient';
	unset($arrURL[$chave]);
}

if(($chave = array_search('onserver', $arrURL)) !== false){
	$_ll['operation_mode'] = 'onserver';
	unset($arrURL[$chave]);
}


/** define o tipo de operação do sistema [app, api, opt]; */
if($_ll['operation_type'] === FALSE){
	foreach ($_ll['operation_types'] as $opt){
		if(($chave = array_search($opt, $arrURL)) !== false){
			$_ll['operation_type'] = $opt;
			unset($arrURL[$chave]);
			break;
		}
	}
}

//echo '<pre>abre '.$_ll['operation_type'].' - '.$_ll['operation_load'].' - '.$_ll['enter_mode'].'</pre>';
//echo '<pre>$_LL = '.print_r($_ll, true).'</pre>';

/** Define o operation_load se ainda não existir */
if(!isset($_ll['operation_load']))
	$_ll['operation_load'] = array_shift($arrURL);



/** Inicia a capitura de erros */
try {


if(in_array($_ll['operation_type'], $_ll['operation_types']) && $_ll['operation_load'] !== NULL){
	
	if(!is_dir(BASE_PATH. '/'. $_ll['operation_type']. '/'. $_ll['operation_load']))
		throw new Exception('O destino de operation_type e operation_load não existe.');
	
}elseif(!in_array($_ll['operation_type'], $_ll['operation_types']) && $_ll['operation_load'] !== NULL){

	/** Verifica o tipo da requisição [OPT, API, APP] */
	foreach($_ll['operation_types'] as $opt){
		if(is_dir(BASE_PATH. '/'. $opt. '/'. $_ll['operation_load'])){
			$_ll['operation_type'] = $opt;
			break;
		}
	}

	/** Caso não emcontre mostra um erro */
	if(!$_ll['operation_type'])
		throw new Exception('Não foi posivel definir um operation_type com o operation_load');
	
}else{

	/** Caso seja para uma requisicao nli */
	if($_ll['enter_mode'] == 'nli'){
		/** Define a requisiçõe default nli do sistema */
		$_ll['operation_type'] = $_ll['default']['nli']['operation_type'];
		$_ll['operation_load'] = $_ll['default']['nli']['operation_load'];
		
	}else{
		/** Define a requisiçõe default login do sistema */
		$_ll['operation_type'] = $_ll['default']['desktop']['operation_type'];
		$_ll['operation_load'] = $_ll['default']['desktop']['operation_load'];
	}
	
}


//	
/** Verifica a existemcia do arquivo de emtrada da requisição */
if(!file_exists(($r = BASE_PATH. '/'. $_ll['operation_type']. '/'. $_ll['operation_load']. '/'. ($_ll['operation_mode']). (!$_ll['terminal']? '.php': '.tmn')))){
	/** se nao existir mostra um erro */
	throw new Exception('O arquivo de requisição não existe: '. $r);
    //$_ll['operation_type'] = 'opt';
    //$_ll['operation_load'] = 'msg';
    //$_ll['operation_mode'] = 'start';
    //$msg_mensage = 'nao_existe';
} 


/****************	teste de segurança	*/
$ll_segok = false;
/* ********************************************************************** CONSTRUIR UM OBJETO PARA O CONFIG 
if(lliure::tsecuryt() == false){ // se não for desenvolverdor
	if(($config = @simplexml_load_file($_ll[$_ll['operation_type']]['pasta'].'/sys/config.ll')) !== false){

		if($config->seguranca != 'public' && (lliure::securyt($_GET['app'])))
			$ll_segok = true;
		elseif($config->seguranca == 'public')
			$ll_segok = true;

	} else {
		$ll_segok = true;
	}
} else {
	$ll_segok = true;
}

*/
$ll_segok = true;

/** se não for seguro */
if(!$ll_segok){
	/** mostra um erro */
	throw new Exception('privado');
}

}catch(Exception $ex){
	if($_ll['terminal'])
		terminal::error($ex->getMessage());
	
	$_ll['operation_type'] = 'opt';
	$_ll['operation_load'] = 'msg';
	$_ll['operation_mode'] = 'start';
	$msg_mensage = $ex->getMessage();
}
  
/** Define os dados do APP da requisição */
$_ll[$_ll['operation_type']]['home'] = $_ll['operation_load'];
$_ll[$_ll['operation_type']]['onserver'] = 'onserver/'.$_ll[$_ll['operation_type']]['home'];
$_ll[$_ll['operation_type']]['onclient'] = 'onclient/'.$_ll[$_ll['operation_type']]['home'];
$_ll[$_ll['operation_type']]['pasta'] = $_ll['operation_type']. '/'. $_ll['operation_load']. '/';
$_ll[$_ll['operation_type']]['pagina'] = 
	$_ll[$_ll['operation_type']]['pasta'].
	(($_ll['enter_mode'] == 'nli')? 'nli/': '').
	($_ll['operation_mode']).
	(!$_ll['terminal']? '.php': '.tmn');


if($_ll['terminal']){
	terminal::execute();
	die();
}

///** caos a requisição nao seja default, ou seja, onclient ou onserver */
//if($_ll['operation_mode'] !== 'default'){
//	/** carrega a requisição e para o sistema */
//	require_once($_ll[$_ll['operation_type']]['pagina']);die();}

/** carrega a requisicao do usuario */
require_once $_ll[$_ll['operation_type']]['pagina'];

//echo '<pre>abre '.$_ll['operation_type'].' - '.$_ll['operation_load'].' - '.$_ll['enter_mode'].'</pre>';
//echo '<pre>$_LL = '.print_r($_ll, true).'</pre>';