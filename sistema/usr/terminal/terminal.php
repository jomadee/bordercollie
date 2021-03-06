<?php

class Terminal{

	public static function start($cmd){
		
		try{
			$type = gettype($r = self::execute($cmd));
		}catch(Exception $exc){
			$type = 'error';
			$r = array('error' => $exc->getMessage());
		}
		
		switch ($type){
			
			case "boolean":
			case "integer":
			case "double":
			case "string":
			case "NULL":
				$r = array($type => $r);
				break;
			
			case "array":
				$k = array_keys($r);
				if(isset($k[0]) && $k[0] == 'action') {
					$r = $r['action'];
					break;
				}
				
			case "object":
				$r = array('object' => $r);
				break;
			
			case "resource":
				$r = array('error' => 'resource não é conpativel');
				break;
			
			case "error":
				break;

			case "unknown type":
			default:
				$r = array('error' => 'retorno não definido');
				break;
		}
		
		echo json_encode($r);

	}
    
    final static function execute($cmd){

		if(!(is_string($cmd) || is_array($cmd)))
			throw new Exception('ERROR: comando passado ao termanima tem de ser uma string ou um array');

        $type = false;
		$func = NULL;

		if(is_string($cmd))
        	$cmd = ShortTag::Explode($cmd);

        $keys = array_keys($cmd);

        //return print_r($cmd, true);
        //return (string) self::cmdType($cmd);

        switch (self::cmdType($cmd)){

            case 1:
                $type = $keys[0];
                $mode = array_shift($cmd);
                break;

            case 2:
                $type = array_shift($cmd);

            case 3:
				$mode = array_shift($cmd);
                break;

            default:
                throw new Exception('Comando invalido');
                break;

        }

        self::load($mode, $type);
		
        $keys = array_keys($cmd);

		//return '$mode = '. $mode. ' $type = '. ($type? 'true': 'false');
		//return ('host' == 0?  'true': 'false');

		if(isset($keys[0]) && $keys[0] === 0 && method_exists($mode.'_tmn', $cmd[0]))
			$func = array_shift($cmd);
		
        elseif(method_exists($mode.'_tmn', $mode))
			$func = $mode;
		
        else
            throw new Exception('Comando não definido');

		return call_user_func(array($mode.'_tmn', $func), $cmd);

    }
	
	final static function error($msg){
		echo json_encode(array('texto' => $msg));
		die();
	}
    
    final private static function cmdType(array $cmd){

		global $_ll;
		$type = 0;
        $keys = array_keys($cmd);

        if(isset($keys[0]) && in_array($keys[0], $_ll['operation_types']))
            $type = 1;

        elseif(isset($cmd[0]) && in_array($cmd[0], $_ll['operation_types']) && isset($cmd[1]) && is_string($cmd[1]))
            $type = 2;

        elseif(isset($cmd[0]) && is_string($cmd[0]))
            $type = 3;

        return $type;

    }
	
	public static final function load($mode, $type = false){

        global $_ll;

        if(!$type) {
            foreach ($_ll['operation_types'] as $opt) {
                if (is_dir(BASE_PATH . '/' . $opt . '/' . $mode)) {
                    $type = $opt;
                    break;
                }
            }
        }

		//return print_r(array('$type' => $type, '$mode' => $mode, '$func' => $func), TRUE);

        if(!$type)
            throw new Exception('Não foi posivel definir um operation_type com o operation_load');

        if(!file_exists(($f = BASE_PATH. DS. $type. DS. $mode. DS. $mode. '.tmn')))
            throw new Exception('Esta aplicação não possui classe de terminal.');

        else require_once $f;
		
		return true;
	}
	
	public static function action($comands){
		return array('action' => $comands);
	}
	
	public static function read($text, $return = ''){
		return self::action(array('read' => $text, 'prefixo' => $return));
	}
	
	public static function password($text, $return = ''){
		return  self::action(array('password' => $text, 'prefixo' => $return));
	}
	
	public static function script($script){
		return  self::action(array('script' => $script));
	}

}