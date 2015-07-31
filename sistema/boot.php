<?php
if(phpversion() < 5){
	echo 'Seu servidor PHP está na versão '.phpversion().' para o funcionamento pleno do sistema é exigido a versão 5';
	die();
}

set_include_path(get_include_path() . PATH_SEPARATOR . '../../');

/*********   Definição da url real   *********/
$arrUrl = substr($_SERVER['REQUEST_URI'], 0, (($a = strpos($_SERVER['REQUEST_URI'], '?')) !== false ? $a : strlen($_SERVER['REQUEST_URI'])));
$uReal = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.str_replace('usr/lliure/boot.php','', $_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']);

/* verifica se tem a "/" final na url se tiver remove */
if($arrUrl{strlen($arrUrl) - 1} == '/')
	$arrUrl = substr($arrUrl, 0, -1);
	
$arrUrl = explode('/', $arrUrl);
$nReal = explode('/', $uReal);

for($i = 0; $i <= count($nReal)-4; $i++)
	unset($arrUrl[$i]);
		
$arrUrl = array_values($arrUrl);

$_ll['operation_mode'] = 'default';
$_ll['enter_mode'] = 'default';
$_ll['terminal'] = false;
$_ll['operation_type'] = false;

require_once('usr/lliure/ll.php');

/*
if(array_search('onserver', $arrUrl) !== false)
	$_ll['operation_mode'] = 'onserver';
	
if(array_search('onclient', $arrUrl) !== false)
	$_ll['operation_mode'] = 'onclient';
	
if(array_search('nli', $arrUrl) !== false || !ll::get_login())
	$_ll['enter_mode'] = 'nli';	
	
if(array_search('terminal', $arrUrl) !== false)
	$_ll['terminal'] = true;
	
if(array_key_exists('app', $arrUrl) !== false)
	$_ll['operation_type'] = 'app';
	
if(array_key_exists('opt', $arrUrl) !== false)
	$_ll['operation_type'] = 'opt';
	
if(array_key_exists('api', $arrUrl) !== false)
	$_ll['operation_type'] = 'api';
*/

if(($chave = array_search('terminal', $arrUrl)) !== false){
	$_ll['terminal'] = true;
	unset($arrUrl[$chave]);
}

if(($chave = array_search('nli', $arrUrl)) !== false || !ll::get_login()){
	$_ll['enter_mode'] = 'nli';
	if($chave != false) 
		unset($arrUrl[$chave]);
}

/**********************************************		Acesso terminal	*/
if($_ll['terminal']){
	ll::terminal();
	die();
}

if(($chave = array_search('onserver', $arrUrl)) !== false){
	$_ll['operation_mode'] = 'onserver';
	unset($arrUrl[$chave]);
}
	
if(($chave = array_search('onclient', $arrUrl)) !== false){
	$_ll['operation_mode'] = 'onclient';
	unset($arrUrl[$chave]);
}


if(($chave = array_search('app', $arrUrl)) !== false){
	$_ll['operation_type'] = 'app';
	unset($arrUrl[$chave]);
}
	
if(($chave = array_search('opt', $arrUrl)) !== false){
	$_ll['operation_type'] = 'opt';
	unset($arrUrl[$chave]);
}
	
if(($chave = array_search('api', $arrUrl)) !== false){
	$_ll['operation_type'] = 'api';
	unset($arrUrl[$chave]);
}


/******************************************		Define as definições de APP/OPT	*/

if(($_ll['operation_load'] = array_shift($arrUrl)) !== null){	
	
	if($_ll['operation_type'] != false ){
		if(!file_exists($_ll['operation_type'].'/'.$_ll['operation_load'])){
			$_ll['operation_type'] = 'opt';
			$_ll['operation_load'] = 'msg';
				
			$msg_mensage = $_ll['operation_type'].'_nao_existe';
		}		
	} else { 
		if(file_exists('opt/'.$_ll['operation_load'])){
			$_ll['operation_type'] = 'opt';
		} elseif(file_exists('api/'.$_ll['operation_load'])){
			$_ll['operation_type'] = 'api';
		} elseif(file_exists('app/'.$_ll['operation_load'])){
			$_ll['operation_type'] = 'app';
		} else {
			$_ll['operation_type'] = 'opt';
			$_ll['operation_load'] = 'msg';
				
			$msg_mensage = 'app_nao_existe';
		}
	}	
} else {
	if(!in_array($_ll['operation_type'], array('app', 'api', 'opt')))
		$_ll['operation_type'] = 'opt';
		
	if(empty($_ll['operation_load'])){
		$_ll['operation_type'] = 'opt';
		$_ll['operation_load'] = 'desktop';
		
		if($_ll['enter_mode'] == 'nli')
			$_ll['operation_load'] = 'loguser';
	}
}

echo '<pre>abre '.$_ll['operation_type'].' - '.$_ll['operation_load'].' - '.$_ll['enter_mode'].'</pre>';

/**********************************************		CARREGA APP/OPT	*/
	if(!file_exists($_ll['operation_type'].'/'.$_ll['operation_load'].'/'.($_ll['operation_mode'] !== 'default' ? $_ll['operation_mode'] : 'start').'.php')){
		$_ll['operation_type'] = 'opt';
		$_ll['operation_load'] = 'msg';
		$_ll['operation_mode'] = 'default';
			
		$msg_mensage = 'nao_existe';
	} 
	/****************	teste de segurança	*/
	$ll_segok = false;
	/* ********************************************************************** CONSTRUIR UM OBJETO PARA O CONFIG 
	if(ll::tsecuryt() == false){ // se não for desenvolverdor
		if(($config = @simplexml_load_file($_ll[$_ll['operation_type']]['pasta'].'/sys/config.ll')) !== false){
			
			if($config->seguranca != 'public' && (ll::securyt($_GET['app'])))
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
	
	if(!$ll_segok){
		$_ll['operation_type'] = 'opt';
		$_ll['operation_load'] = 'msg';
		
		$msg_mensage = 'privado';
	}

  

$_ll[$_ll['operation_type']]['home'] = $_ll['operation_load'];
$_ll[$_ll['operation_type']]['onserver'] = 'onserver/'.$_ll[$_ll['operation_type']]['home'];
$_ll[$_ll['operation_type']]['onclient'] = 'onclient/'.$_ll[$_ll['operation_type']]['home'];
$_ll[$_ll['operation_type']]['pasta'] = $_ll['operation_type'].'/'.$_ll['operation_load'].'/';

$_ll[$_ll['operation_type']]['pagina'] = $_ll[$_ll['operation_type']]['pasta'];
	
if($_ll['enter_mode'] == 'nli')	
	$_ll[$_ll['operation_type']]['pagina'] .= 'nli/';
	
if($_ll['operation_mode'] !== 'default')
	$_ll[$_ll['operation_type']]['pagina'] .= $_ll['operation_mode'].'.php';
else
	$_ll[$_ll['operation_type']]['pagina'] .= 'start.php';
	
	
	

	require_once($_ll[$_ll['operation_type']]['pagina']);


/***/
echo '<pre>abre '.$_ll['operation_type'].' - '.$_ll['operation_load'].' - '.$_ll['enter_mode'].'</pre>';
echo '<pre>$_LL = '.print_r($_ll, true).'</pre>';

?>