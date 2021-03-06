<?php

class Instal extends DB{

    public function __construct(){
        parent::__construct('');
    }

    public static function rum($dados){

        $db = Confgs::getFile(BASE_PATH. '/opt/instal/base/db.confg.ll');
        $db['basetype']  = $dados['type'];
        $db['hostName']  = $dados['host'];
        $db['userName']  = $dados['user'];
        $db['password']  = $dados['pass'];
        $db['tableName'] = $dados['table'];

        $confg = Confgs::getFile(BASE_PATH. '/opt/instal/base/confg.ll');
        $confg['defines']['PREFIXO'] = $dados['prefixo'];

        Instal::confgFiles($confg, $db);
        return array_merge(
            array('files' => array(
                'confg.ll' => array(
                    'arquivo' => BASE_PATH. '/opt/instal/base/db.confg.ll',
                    'status' => 'OK'
                ), 'db.confg.ll' => array(
                    'arquivo' => BASE_PATH. '/opt/instal/base/confg.ll',
                    'status' => 'OK'
                ),
            )),
            array('querys' => Instal::sql(BASE_PATH. '/opt/instal/base/db.sql', PREFIXO, $dados['prefixo']))
        );
    }

    private function query($query){
        return $this->exec($query) !== false? 'OK': $this->error();
    }

    private static function confgFiles(array $config, array $db){
        Confgs::putFile(BASE_PATH. '/etc/confg.ll', $config);
        Confgs::putFile(BASE_PATH. '/etc/db.confg.ll', $db);
    }

    public static function sql($file, $prefixo_atual = false, $prefixo_novo = false){

        /* Verifica se o arquivo existe */
        if (!file_exists($file)){
            throw new Exception('File n�o existe.', 0);
            return null;
        }

        /* Carrega o onteudo do arquivo */
        if(($conteudo = file($file)) === false){
            throw new Exception('Falha ao carregar o arquivo.', 1);
            return null;
        }

        /* Crio as querys */
        $i = 0; $querys = array();
        foreach($conteudo as $linha){

            // Remove espa�os em branco nas "bordas"
            $linha = trim($linha);

            // Caso for linha em branco ou linha com coment�rio, "pula" a mesma
            if(empty($linha) || (substr($linha, 0, 1) == '#') || (substr($linha, 0, 2) == '--'))
                continue;

            // Adiciona quebra de linha e instru��es da mesma
            $querys[$i] = (!isset($querys[$i]) ? "\n" . $linha : $querys[$i] . "\n" . $linha);

            // Encontrado um ";" no final da linha, ent�o, instru��o encerrada.
            // Pula para o pr�ximo �ndice do array
            if(substr($linha, -1, 1) == ';')
                ++$i;

        }

        /* Separo as querys que vou usar */
        $i = 0; $q = array();
        foreach($querys as $query){

            // Limpa os ";"
            $query = rtrim($query, ';');

            /* Cria tabela */
            if(preg_match('/CREATE (TEMPORARY )?TABLE (IF NOT EXISTS )?(\S+)/i', $query, $results)){
                $table = trim($results[3], '`');
                $query = self::altera_prefixo('/(CREATE (TEMPORARY )?TABLE (IF NOT EXISTS )?\`?)('. $prefixo_atual. ')/i', '$1'. $prefixo_novo, $query, $prefixo_atual, $prefixo_novo);
                $q[++$i. ': CREATE TABLE '. $table] = $query;

            /* Inceri em uma tabela */
            }elseif(preg_match('/INSERT (LOW_PRIORITY |DELAYED |HIGH_PRIORITY )?(IGNORE )?(INTO )?(\S+)/i', $query, $results)){
                $table = trim($results[4], '`');
                $query = self::altera_prefixo('/(INSERT (LOW_PRIORITY |DELAYED |HIGH_PRIORITY )?(IGNORE )?(INTO )?\`?)('. $prefixo_atual. ')/i', '$1'. $prefixo_novo, $query, $prefixo_atual, $prefixo_novo);
                $q[++$i. ': INSERT INTO '. $table] = $query;

            /* Deleta tabelas */
            }elseif(preg_match('/(DROP +(TEMPORARY +)?TABLE +(IF +EXISTS +)?)(.+)(RESTRICT|CASCADE)/i', $query, $results)){
                $table = false;
                $tables = explode(',', $results[4]);
                foreach($tables as $t) $table .= (!$table? '': ', '). self::altera_prefixo('/'. $prefixo_atual. '(\S+)/i', $prefixo_novo. '$1', trim(trim($t), '`'), $prefixo_atual, $prefixo_novo);
                $q[++$i. ': DROP TABLE '. $table] = ($results[1]. $table. $results[5]);

            /* ALTER TABLE */
            }elseif(preg_match('/(ALTER\s+(IGNORE\s+)?TABLE\s+)(\S+)/i', $query, $results)){
                $table = trim($results[3], '`');
                $query = self::altera_prefixo('/(ALTER\s+(IGNORE\s+)?TABLE\s+\`?)('. $prefixo_atual. ')/i', '$1'. $prefixo_novo, $query, $prefixo_atual, $prefixo_novo);
                $q[++$i. ': ALTER TABLE '. $table] = $query;

            }
        }

        /* Executp as querys */
        $db = new Instal();
        foreach($q as $k => $query)
            $q[$k] = array('query' => $query. "\n\n", 'status' => $db->query($query));

        return $q;

    }

    private static function altera_prefixo($pattern, $replacement, $string, $prefixo_atual, $prefixo_novo){
        if($prefixo_atual && $prefixo_novo && $prefixo_atual != $prefixo_novo)
            return preg_replace($pattern, $replacement, $string);
        return $string;
    }

}