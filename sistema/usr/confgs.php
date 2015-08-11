<?php

class Confgs{

    public static function putFile($file, array $dados){

        $content = var_export($dados, true);
        $content = preg_replace('/\n\s+array/im', 'array', $content);
        $content = preg_replace_callback(
            '/\n(\s+)/im',
            create_function( '$matches',
                'return "\n". str_repeat("\t", floor(strlen($matches[1]) / 2));'
            ),
            $content
        );

        file_put_contents($file, '<?php return '. $content. ';');

    }

}