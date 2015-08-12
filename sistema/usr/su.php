<?php

/**
 * Description of SU
 */
class SU extends DB{
	
	public function __construct(){
		parent::__construct(PREFIXO . 'lliure_su');
	}
	
	/**
	 * Pase um <var>$login</var> e <var>$senha</var> para criar um usuario.
	 * Opções adicionais podem ser pasadas em <var>$option</var> em modo de array.
	 * por exp: array('themer' => 'NomeDoTema');
	 * Retorna o id do usuario que acabou de ser criado.
	 * 
	 * @param string $login
	 * @param string $senha
	 * @param array $option
	 * @return int id
	 */
	public function create($login, $senha, array $option = array()){
		$option = parent::antiInjection(array_merge(array('login' => $login, 'senha' => Senha::create($senha)), $option));
		parent::insert($option);
		return parent::insert_id();
	}
	
	/**
	 * Verifica se um usuari com o <var>$login</var> e a <var>$senha</var> existe.
	 * Caso exista retorna o id do usuario.
	 * Se o login não coresponder a algum usuario retorna um Exception (codigo: 0).
	 * Se a senha não coresponder ao usuaria retorna um Exception (codigo: 1).
	 * 
	 * @param string $login
	 * @param string $senha
	 * @return int Id do usuario
	 * @throws Exception 
	 *		(codigo: 0) Usuario não existe<br/>
	 *		(codigo: 1) Senha não coresponde a senha do ususaria
	 */
	public function exist($login, $senha){
		$login = parent::antiInjection($login);
		$senha = parent::antiInjection($senha);
		$q = parent::select('SELECT id, senha FROM '. $this. ' WHERE login="'. $login. '"');
		if(parent::numRows($q) == 0)
			throw new Exception('Usuario não existe', 0);
		$objSenha = new Senha();
		if(!$objSenha->valid($senha, $q[0]['senha']))
			throw new Exception('Senha não coresponde a senha do ususaria', 1);
		return $q[0]['id'];
	}
	
	/**
	 * Carrega os dados de um determinado pelo $id.
	 * Caso exista carrega os dados em
	 * $_SESSION['ll']['user'] e $_ll['user'],
	 * retornando <b>TRUE</b>;
	 * Retorna uma Exception se o usuario não existe.
	 * 
	 * @param integer|string $id
	 * @return boolean 
	 */
	public function login($id){
		global $_ll;
		$id = parent::antiInjection($id);
		$q = parent::select('SELECT id, login, nome, email, foto, grupo, themer FROM '. $this. ' WHERE id="'. $id. '"');
		if(parent::numRows($q) == 0)
			throw new Exception('Usuario não existe', 0);
		$_SESSION['ll']['user'] = $q[0];
		$_ll['user'] = &$_SESSION['ll']['user'];
		return TRUE;
	}
	
}
