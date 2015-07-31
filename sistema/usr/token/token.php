<?php

/**
 * Gerencia a criação e validções de tokens.
 */
class token{
	
	/**
	 * Retorna um tokem.
	 * @return string
	 */
	final static function get(){
		return (isset($_SESSION['ll']['token'])? md5($_SESSION['ll']['token']): self::create());
	}

	/**
	 * Recebe e valida um token comparando ele com o da memoria.
	 * @param string $token Token a ser validados.
	 * @return bolean
	 */
	final static function valid($token){
		return (isset($_SESSION['ll']['token']) && $_SESSION['ll']['token'] == md5($token));
	}

	final private static function create(){
		return ($_SESSION['ll']['token'] = uniqid(md5(rand().':8080')));
	}
	
}
