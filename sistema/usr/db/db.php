<?php
/**
 * Description of db
 *
 * @author Rodrigo
 */

class db {
    
    const 
    PDO = 'PDO',
    MYSQL = 'MYSQL';
    
    protected static

    /**
     * garda a conecção com o banco de dados;
     */
    $DB = NULL,
    $type = NULL;

    protected
  
    /**
     * Nome da tebal ondo ocorerao as consultas.
     */
    $tabela,
            
    /**
     * Pos Fixo temporario do nome da tabla
     */
    $tempPoxFix = NULL,
            
    /**
     * Nome temporario para tebala
     */
    $tempTab = NULL,

    /**
     * Garda uma lista com as ultimas querys executadas.
     */
    $queryList = array();
    
    /**
     * Voce deve sobrescrever este metodo pasando o nome da tabela que sua classe ira gerenciar
     * @param type $tabela o nome da tabela
     */
    public function __construct($tabela) {
        $this->tabela = self::antiInjection($tabela);
    }

    final public function __toString(){
        if ($this->tempTab !== NULL)
            return $this->tempTab;
        else
            return $this->tabela . ($this->tempPoxFix !== NULL? $this->tempPoxFix: '');
    }
    
    final protected function setTempPosFix($tempPoxFix) {
        $this->tempPoxFix = $tempPoxFix;
        return $this;
    }

    final protected function setTempTab($tempTab) {
        $this->tempTab = $tempTab;
        return $this;
    }
    
    final protected function clierTemps(){
        $this->tempPoxFix = NULL;
        $this->tempTab = NULL;
    }

    /**
     * conector com o bamco de dados via PDO ou mysql.
	 * @return PDO|conecsao_mysql
     */
    protected static function conectar(){
		
		if(self::$DB !== NULL)
			return self::$DB;
		
		if(!file_exists(($file = BASE_PATH. DS. 'etc'. DS. 'bdconfg.ll')))
			throw new Exception('Arquivo de configuração de banco de dados não existe.', 0);
		
		$bdconf = require $file;
		
		if(class_exists('PDO')){
			try {
				self::$DB = new PDO($bdconf['basetype']. ':host='. $bdconf['hostName']. ';dbname='. $bdconf['tableName'], $bdconf['userName'], $bdconf['password']);
				self::$type = self::PDO;
				return self::$DB;
			} catch (PDOException $e){
				throw new Exception('Falha de conexão: ' . $e->getMessage(), 1);
			}
		}else{
			if((self::$DB = @mysql_connect($hostname_conexao, $username_conexao, $password_conexao)) === FALSE)
				throw new Exception('<strong>Não foi possivel realizar a conexão com banco de dados</strong><br>verifique as configurações do arquivo bdconf.php em /etc', 1);
			
			mysql_select_db($banco_conexao, self::$DB);
			self::$type = self::MYSQL;
			return self::$DB;
		}
		
    }

    /**
     * adiciona uma query a lista de query esecuradas.
     * @param type $query a query que foi execurada
     * @return String a query q acaba de ser inserida.
     */
    protected function setQueryList($query){
        $this->queryList[] = $query;
        return $query;
    }
    
    /**
     * retorna uma ou mais query executadas.
     * @param mixer $quant  $quant = null<br/>
     *                      retorna a ultima query inserida na lista.
     * 
     *                      $quant = TRUE<br/>
     *                      Retorna toda a lista de querys feiras.
     * 
     *                      $quant = (<i>numero</i>)<br/>
     *                      Retorna a quantidade passada em <b>$quant</b> das ultimas querys feitas.
     * @return mixer
     */
    public function getQueryList($quant = null){
        $retorno = null;
        if (!empty($this->queryList)){
            if ($quant === null)
                $retorno = $this->queryList[count($this->queryList) - 1];
            elseif ($quant === true)
                $retorno = $this->queryList;
            elseif (is_numeric($quant))
                for($i = (((count($this->queryList) - $quant) <= 0)? 0: count($this->queryList) - $quant); $i < count($this->queryList); $i++)
                    $retorno[] = $this->queryList[$i];
        }
        return $retorno;
    }
    /**
     * Printa o Log compreto de querys esecutados. O mesmo que <code>getQueryList(TRUE)</code>
     */
    public function queryLog(){
        echo '<pre>', print_r($this->getQueryList(TRUE), TRUE), '</pre>';
    }
    
    /**
     * Trata o conteudo inserido com diretizes de anti injection, se o conteudo <br/>
     * inserido for uma string ele a trada e devolve uma string, caso seja um <br/>
     * array ele trata seus vaores recurcivamnete e retorna o array tratado.
     * @param mixed $sql Trata se for uma string ou array outros valores como <br/>
     * <code>boolean</code> ou <code>null</code> s�o preservados.
     * @return mixed retorna o conteudo inserido tratodo.
     */
    static function antiInjection($sql){
        if(is_array($sql)){
            foreach($sql as $chave => $valor)
                $sql[self::antiInjection($chave)] = self::antiInjection($valor);
        }elseif(is_string($sql)){
            $sql = preg_replace("/(%0a|%0d|Content-Type:|bcc:|to:|cc:|Autoreply:|from|select|insert|delete|where|drop table|show tables|\\\\)/i", "", $sql);
            $sql = trim($sql); # Remove espa�os vazios.
            $sql = addslashes($sql); # Adiciona barras invertidas � uma string.
        }
        return $sql;
    }
    
    /**
     * Retorna uma string com todas as <b><i>ShortTegs</i></b> subsistidas por seu<br/>
     * correspondente valor contido no array de dados, isto �, localiza-se um <br/>
     * �ndice no array de dados com a teg, e ela � substitu�da por esse valor.<br/>
     * O array <b>dados</b> � passado por referencia, e para cada ShortTeg <br/>
     * encontada o indice e removido do array, isto �, este metodo retira do seu <br/>
     * array os indices que coresponder�o com alguma ShortTeg, A n�o ser que o<br/>
     * parametro <i>naoDeletar</i> seta <b>TRUE</b>, neste caso, ele nao remove <br/>
     * os indices.
     * 
     * @param String $stringShortTag A string contendo as ShortTegs exp.: "id='[id]'"
     * @param array $dados o array com os dados a serem subistituidas.
     * @param boolean $naoDeletar caso ele seta <b>TRUE</b> nao s�o deletados os <br/>
     * indices.
     * 
     * @return String A sting com as ShortTegs subistituidas.
     */
    final protected static function shortTagReplace($stringShortTag, array &$dados, $naoDeletar = FALSE){
        $retorno = $stringShortTag;
        foreach ($dados as $key => $value) {
            $retorno = preg_replace('/\['.$key.']/i', $value, $retorno, -1, $count);
            if ($count > 0 && !$naoDeletar){
                unset($dados[$key]);
            }
        }
        return $retorno;
    }
    
    /**
     * Retorna uma linha de um Resultado de pesquisa.
     * @param array $result
     * @return array linha do resultado.
     */
    final static function fetch(array &$result){
        $retorno = current($result);
        if ($retorno !== FALSE)
            next($result);
        return $retorno;
    }
    
    /**
     * Retorna a quantidade de linhas que a consulta.
     * @param array $result resultado da consulta.
     * @return int
     */
    final static function numRows(array $result){
        return count($result);
    }

    /**
     * O ID gerado para uma coluna AUTO_INCREMENT pela consulta anterior em caso<br/> 
     * de sucesso, 0 se a consulta anterior n�o gerar um valor AUTO_INCREMENT,<br/> 
     * ou FALSE se n�o houver conex�o MySQL foi criado.
     * @return int
     */
    final static function insert_id(){
        if (self::$type == self::MYSQL){
            return mysql_insert_id(self::$DB);
        }elseif(self::$type == self::PDO){
            return self::$DB->lastInsertId();
        }
    }
    
    /**
     * Apelido da fun��o <b><i>insert_id</i></b>.
     * @return type
     */
    final static function lastInsertId(){
        return self::insert_id();
    }
    
    /**
     * Executa consulta ao banco de dados que nao gerao resultado.
     * @param type $query A consulta SQL para executar (normalmente um INSERT, UPDATE, ou DELETE).
     * @return boolean <b>TRUE</b> se a consulta sucesso, <b>FALSE</b> em caso de falha.
     */
    final protected function exec($query, $persistir = FALSE){
		if(self::$DB === NULL)self::conectar();
        if(!$persistir)$this->clierTemps();
        $this->setQueryList($query);
        if (self::$type == self::MYSQL){
            $erro = mysql_query($this->getQueryList(), self::$DB);
            if($erro === FALSE)
                return false;
            elseif($erro === TRUE)
                return mysql_affected_rows(self::$DB);
        }elseif(self::$type == self::PDO){
            return self::$DB->exec($this->getQueryList());
        }
    }
    
    /**
     * Execulta uma query que tenha um resulta como SELECT.
     * @param string $query a query a ser execultada.
     * @return array um result em formato de array.
     */
    final protected function select($query, $persistir = FALSE){
		if(self::$DB === NULL)self::conectar();
        if(!$persistir)$this->clierTemps();
        $this->setQueryList($query);
        if (self::$type == self::MYSQL){
            $result = mysql_query($this->getQueryList(), self::$DB); $return = array();
            while (($return[] = mysql_fetch_assoc($result)) or array_pop($return));
            return $return;
        }elseif(self::$type == self::PDO){
            return self::$DB->query($this->getQueryList())->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    /**
     * Cria um INSERT com os dados pasados no array.
     * 
     * Este array pode ser passado de duas formas.
     * 
     * Forma 1, array simples.<br/>
     * Exp.:<br/>
     * <pre>array(
     * &nbsp;&nbsp;&nbsp;&nbsp;[nome] => Arnaldo,
     * &nbsp;&nbsp;&nbsp;&nbsp;[sobrenome] => da Silva
     * )</pre>
     * 
     * 
     * Forma 2, array com 1 subnivel.<br/>
     * Exp.:<br/>
     * <pre>array(
     * &nbsp;&nbsp;&nbsp;&nbsp;[0] => array(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[nome] => Arnaldo,
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[sobrenome] => da Silva
     * &nbsp;&nbsp;&nbsp;&nbsp;),
     * &nbsp;&nbsp;&nbsp;&nbsp;[1] => array(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[nome] => Amaral,
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[sobrenome] => da Costa
     * &nbsp;&nbsp;&nbsp;&nbsp;)
     * )</pre>
     * 
     * @param array $dados
     */
    final protected function insert(array $dados){
        $dados = self::antiInjection($dados);
        
        $chaves = array_keys($dados);
        if (!is_array($dados[$chaves[0]]))
            $dados = array($dados);
        
        $colunas = null;
        foreach ($dados as $valor)
            foreach ($valor as $chave => $dado)
                $colunas[$chave] = $chave;

        $VALUES = '';
        foreach ($dados as $value){
            $linha = '';
            foreach ($colunas as $coluna){
                $linha .= ($linha == ''? '': ', ') . (isset($value[$coluna]) && self::is_null($value[$coluna])? 'NULL' : '"'.$value[$coluna].'"');
            }
            $VALUES .= ($VALUES == ''? '': ', ') . '('.$linha.')';
        }
        
        $this->exec('INSERT '.$this.' ('.implode(', ', $colunas).') VALUES '.$VALUES);
        
    }
    
    /**
     * Cria um ou varios UPDATE com os dados pasados no array e com o WHELE definido.
     * 
     * Este array pode ser passado de duas formas.
     * 
     * Forma 1, array simples.<br/>
     * Exp.:<br/>
     * <pre>array(
     * &nbsp;&nbsp;&nbsp;&nbsp;[id] => 0,
     * &nbsp;&nbsp;&nbsp;&nbsp;[nome] => Arnaldo,
     * &nbsp;&nbsp;&nbsp;&nbsp;[sobrenome] => da Silva
     * )</pre>
     * 
     * 
     * Forma 2, array com 1 subnivel.<br/>
     * Exp.:<br/>
     * <pre>array(
     * &nbsp;&nbsp;&nbsp;&nbsp;[0] => array(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[id] => 0,
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[nome] => Arnaldo,
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[sobrenome] => da Silva
     * &nbsp;&nbsp;&nbsp;&nbsp;),
     * &nbsp;&nbsp;&nbsp;&nbsp;[1] => array(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[id] => 1,
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[nome] => Amaral,
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[sobrenome] => da Costa
     * &nbsp;&nbsp;&nbsp;&nbsp;)
     * )</pre>
     * 
     * <b>OBS</b>.: Quando pasado desta maneira varios updates ser�o feitos.
     * 
     * O WHELE � constrido com ShotTegs e � subistituido pelo valor corespondente<br/>
     * no array de dados.<br/>
     * Exp.:<br/>
     * Tome para este exemplo o array da forma 1.<br/>
     * $where = 'id="[id]"';
     * 
     * RESULTADO:<br/>
     * UPDATE (tabela) SET `nome`="Arnaldo", `sobrenome`="da Silva" WHERE id="0";
     * 
     * <b>OBS</b>.: os indices que coresponderer a alguma sortTeg no WHERE n�o s�o colocados
     * como valorer serem upados.
     * 
     * @param array $dados
     * @param string $where
     */
    final protected function update(array $dados, $where = NULL){
        
        $dados = self::antiInjection($dados);
        
        $chaves = array_keys($dados);
        if (!is_array($dados[$chaves[0]]))
            $dados = array($dados);

        foreach ($dados as $dado){

            $w = self::shortTagReplace($where, $dado);
            
            $valores = '';
            foreach($dado as $chaves => $valor)
                $valores .= (empty($valores)? '': ', ') . '`' . $chaves . '`=' . (self::is_null($valor)? 'NULL': '"'.$valor.'"');
            
            $this->exec('UPDATE '.$this.' SET '.$valores.' WHERE '.$w.' ;', true);
            
        }
        $this->clierTemps();
    }
    
    /**
     * Cria um ou varios DELETE com os dados pasados no array e com o WHELE definido.
     * 
     * Este array pode ser passado de duas formas.
     * 
     * Forma 1, array simples.<br/>
     * Exp.:<br/>
     * <pre>array(
     * &nbsp;&nbsp;&nbsp;&nbsp;[id] => 0,
     * &nbsp;&nbsp;&nbsp;&nbsp;[nome] => Arnaldo,
     * &nbsp;&nbsp;&nbsp;&nbsp;[sobrenome] => da Silva
     * )</pre>
     * 
     * 
     * Forma 2, array com 1 subnivel.<br/>
     * Exp.:<br/>
     * <pre>array(
     * &nbsp;&nbsp;&nbsp;&nbsp;[0] => array(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[id] => 0,
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[nome] => Arnaldo,
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[sobrenome] => da Silva
     * &nbsp;&nbsp;&nbsp;&nbsp;),
     * &nbsp;&nbsp;&nbsp;&nbsp;[1] => array(
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[id] => 1,
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[nome] => Amaral,
     * &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;[sobrenome] => da Costa
     * &nbsp;&nbsp;&nbsp;&nbsp;)
     * )</pre>
     * 
     * <b>OBS</b>.: Quando pasado desta maneira varios deletes ser�o feitos.
     * 
     * O WHELE � constrido com ShotTegs e � subistituido pelo valor corespondente<br/>
     * no array de dados.<br/>
     * Exp.:<br/>
     * Tome para este exemplo o array da forma 1.<br/>
     * $where = 'id="[id]"';
     * 
     * RESULTADO:<br/>
     * DELETE FROM (tabela) WHERE id="0";
     * 
     * <b>OBS</b>.: os indices que coresponderer a alguma sortTeg no WHERE n�o s�o colocados
     * como valorer serem upados.
     * 
     * O <code>$where</code> � opcional e se nao pasodo o DELETE � montado de maneira a <br/>
     * todos os indices estaren no WHERE.<br/>
     * EXP.:<br/>
     * Tome para este exemplo o array da forma 1.
     * 
     * RESULTADO:<br/>
     * DELETE FROM (tabela) WHERE id="0" and nome="Amaral" and sobrenome="da Silva";
     * 
     * @param array $dados
     * @param string $where
     */
    final protected function delite(array $array, $where = NULL){
        $array = self::antiInjection($array);
        
        $keys = array_keys($array);
        if (!is_array($array[$keys[0]]))
            $array = array($array);

        foreach ($array as $key => $value){
            $w = '';
            if ($where !== NULL){
                $w = $this->shortTagReplace($where, $value);
            }else{
                foreach ($value as $chave => $valor)
                    if (is_string($chave))
                        $w .= ($w=''?'':' and ').$chave.' '. (self::is_null($valor)? 'IS NULL' : '= "'.$valor.'"');
            }
            
            $this->exec('DELETE FROM '.$this.' WHERE '.$w.' ;', true);
        }
        $this->clierTemps();
    }
	
	final static function is_null($var){
		return ($var === NULL || (strlen($var) == 4 && preg_match('/^null$/i', $var) === 1));
	}
    
}