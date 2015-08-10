<?php

class Instal extends db{

    public function __construct(){
        parent::__construct('');
    }

    private function query($query){
        return $this->exec($query) !== false? 'OK': $this->error();
    }

    public static function confgFiles(array $config, array $db){
        file_put_contents(BASE_PATH. '/etc/confg.ll', '<?php return '. var_export($config, true). ';');
        file_put_contents(BASE_PATH. '/etc/db.confg.ll', '<?php return '. var_export($db, true). ';');
    }

    public static function sql($file, $prefixo_atual = false, $prefixo_novo = false){

        var_dump($prefixo_atual, $prefixo_novo);

        /* Verifica se o arquivo existe */
        if (!file_exists($file)){
            throw new Exception('File não existe.', 0);
            return null;
        }

        /* Carrega o onteudo do arquivo */
        if(($conteudo = file($file)) === false){
            throw new Exception('Falha ao carregar o arquivo.', 1);
            return null;
        }

        $i = 0; $querys = array();

        /* Formatações e limpezas */
        foreach($conteudo as $linha){

            // Remove espaços em branco nas "bordas"
            $linha = trim($linha);

            // Caso for linha em branco ou linha com comentário, "pula" a mesma
            if(empty($linha) || (substr($linha, 0, 1) == '#') || (substr($linha, 0, 2) == '--'))
                continue;

            // Adiciona quebra de linha e instruções da mesma
            $querys[$i] = (!isset($querys[$i]) ? "\n" . $linha : $querys[$i] . "\n" . $linha);

            // Encontrado um ";" no final da linha, então, instrução encerrada.
            // Pula para o próximo índice do array
            if(substr($linha, -1, 1) == ';')
                ++$i;

        }

        $i = 0; $q = array();

        foreach($querys as $query){

            // Limpa os ";"
            $query = rtrim($query, ';');

            /* Cria tabela */
            if(preg_match('/CREATE (TEMPORARY )?TABLE (IF NOT EXISTS )?(\S+)/i', $query, $results)){
                $table = trim($results[3], '`');
                $query = self::altera_prefixo('/(CREATE (TEMPORARY )?TABLE (IF NOT EXISTS )?\`?)('. $prefixo_atual. ')/i', '$1'. $prefixo_novo, $query, $prefixo_atual, $prefixo_novo);
                $q[++$i. ': CREATE TABLE '. $table] = $query;
            }

            /* Inceri em uma tabela */
            elseif(preg_match('/INSERT (LOW_PRIORITY |DELAYED |HIGH_PRIORITY )?(IGNORE )?(INTO )?(\S+)/i', $query, $results)){
                $table = trim($results[4], '`');
                $query = self::altera_prefixo('/(INSERT (LOW_PRIORITY |DELAYED |HIGH_PRIORITY )?(IGNORE )?(INTO )?\`?)('. $prefixo_atual. ')/i', '$1'. $prefixo_novo, $query, $prefixo_atual, $prefixo_novo);
                $q[++$i. ': INSERT INTO '. $table] = $query;
            }

            /* Deleta tabelas */
            elseif(preg_match('/(DROP +(TEMPORARY +)?TABLE +(IF +EXISTS +)?)(.+)(RESTRICT|CASCADE)/i', $query, $results)){
                $table = false;
                $tables = explode(',', $results[4]);
                foreach($tables as $t) $table .= (!$table? '': ', '). self::altera_prefixo('/'. $prefixo_atual. '(\S+)/i', $prefixo_novo. '$1', trim(trim($t), '`'), $prefixo_atual, $prefixo_novo);
                $q[++$i. ': DROP TABLE '. $table] = ($results[1]. $table. $results[5]);
            }

            /* ALTER TABLE */
            elseif(preg_match('/(ALTER\s+(IGNORE\s+)?TABLE\s+)(\S+)/i', $query, $results)){
                $table = trim($results[3], '`');
                $query = self::altera_prefixo('/(ALTER\s+(IGNORE\s+)?TABLE\s+\`?)('. $prefixo_atual. ')/i', '$1'. $prefixo_novo, $query, $prefixo_atual, $prefixo_novo);
                $q[++$i. ': ALTER TABLE '. $table] = $query;

            }
        }

        $bd = new Instal();

        foreach($q as $k => $query)
            $q[$k] = array('query' => $query. "\n\n", 'status' => $bd->query($query));

        return print_r($q, true);

    }

    private static function altera_prefixo($pattern, $replacement, $string, $prefixo_atual, $prefixo_novo){
        if($prefixo_atual && $prefixo_novo && $prefixo_atual != $prefixo_novo)
            return preg_replace($pattern, $replacement, $string);
        return $string;
    }

}