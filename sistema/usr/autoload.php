<?php

class AutoLoad{
    
    static private

	/** array com os functions que o auto load executa */
	$functions = array(),

	/** array com as pasta onde ele procura */
	$paths = array();
    
    /**
     * passe uma function que retorna um processamento para o nome 
     * da classe que esta sendo criada.
     * 
     * @param callable $function
     */
    public static function setFunction($function){
        if (is_callable($function))
            self::$functions[] = $function;
    }
    
    /**
     * pase uma pasta onde ele ira procurar seus objetos.
     * 
     * @param string $path
     */
    public static function setPath($path, $prioridade = 0){
		$reordenar = !isset(self::$paths[$prioridade]);
		
        if (is_dir($path))
            self::$paths[$prioridade][] = $path;
		
		if($reordenar){
			ksort(self::$paths);
			self::$paths = array_reverse(self::$paths);
		}
    }

    public static function getFile($nome){
		
        $arfp = explode(DIRECTORY_SEPARATOR, $nome);
        $file = strtolower(array_pop($arfp));
        $path = implode(DIRECTORY_SEPARATOR, $arfp). (!empty($arfp)? DIRECTORY_SEPARATOR: '');
        
        $r = NULL;
        if (is_readable(($r = self::procuraArquivo($path, $file)))
        || (!empty(self::$functions) && is_readable(($r = self::execFunctions($nome, $path, $file)))))
            return $r;
            
        else
            throw new ErrorException('Erro do AutoLoad');
        
    }
    
    private static function execFunctions($nome, $path, $file){
        foreach (self::$functions as $function)
            if(($r = call_user_func_array($function, array($nome, $path, $file))) != NULL)
                return $r;
        
        return NULL;
    }
    
    private static function procuraArquivo($path, $file){
        foreach (self::$paths as $n)
			foreach ($n as $p)
				if(!empty($p) && ($r = NULL)
				|| file_exists(($r = $p. DIRECTORY_SEPARATOR. $path. $file. DIRECTORY_SEPARATOR. $file. '.ll'))
				|| file_exists(($r = $p. DIRECTORY_SEPARATOR. $path. $file. DIRECTORY_SEPARATOR. $file. '.php'))
				|| file_exists(($r = $p. DIRECTORY_SEPARATOR. $path. $file. '.ll'))
				|| file_exists(($r = $p. DIRECTORY_SEPARATOR. $path. $file. '.php')))
					return $r;
        
        return NULL;
    }
    
}

function autoloadFunction($nome){
	try{
		require_once AutoLoad::getFile($nome);
	}catch(Exception $exc){
		return NULL;
	}
}

if(version_compare(PHP_VERSION, '5.1.2', '>=')){
    if(version_compare(PHP_VERSION, '5.3.0', '>='))
        spl_autoload_register('autoloadFunction', true, true);
    else
        spl_autoload_register('autoloadFunction');
}else{
    function __autoload($nome){autoloadFunction($nome);}
}