<?php
class lliure{
	/*********************************************	Methodos para tratamento de login **********/
	private static $user = false;
	//private static $login = false;
	private static $login = true; //////////////

	public static function set_login(){
		return true;
	}	
	
	public static function set_logout(){
		return true;
	}
	
	public static function get_login(){
		return self::$login;
	}
	
	public static function get_user(){
		return self::$user;
	}
	
	public static function terminal($cmd){
		Terminal::execute($cmd);
	}
	
	/*******************************************************************************************/
}