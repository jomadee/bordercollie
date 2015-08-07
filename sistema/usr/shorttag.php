<?php

class ShortTag{

    static final function Explode($cmd){

        if(!is_string($cmd))
            throw new Exception('O comando não é uma string', 0);

        $cmd = trim(trim(trim($cmd), '[]'));
        $array  = array();

        if(preg_match_all('/(\'(?:[^\'\\\\]|\\\\.)*\'|"(?:[^"\\\\]|\\\\.)*"|[^\\s=]*)([\\s=])/m', $cmd.' ', $parts) > 0)
        foreach($parts[1] as $key => $value){

            $vasia = (strlen($value) <= 0);
            $value = ((isset($value[0]) && ($value[0] == '"' || $value[0] == "'"))? str_replace('\\"', '"', substr($value, 1, -1)): $value);

            switch($parts[2][$key]) {

                case '=':

                    if (!isset($pont)){
                        if(!$vasia){
                            if (!isset($array[$value]))
                                $array[$value] = '';
                            $pont = &$array[$value];
                        }else{
                            $array[] = '';  end($array);
                            $pont = &$array[key($array)];
                        }

                    } else {
                        if(!$vasia){
                            if (!isset($pont[$value]))
                                $pont[$value] = '';
                            $pont = &$pont[$value];
                        }else{
                            $pont[] = '';  end($pont);
                            $pont = &$pont[key($pont)];
                        }
                    }

                    break;
                default:

                    if (!$vasia){
                        if (!isset($pont)){
                            $array[] = $value;

                        }else{
                            $pont = $value;
                            unset($pont);
                        }
                    }elseif(isset($pont)){
                        unset($pont);
                    }

                    break;
            }

        };

        //throw new Exception(print_r($parts, true));
        //throw new Exception(print_r($array, true));

        return $array;

    }


    static final function Implode(array $array){
        return self::IR($array);
    }

    private static final function IR(array $array, $base = null){

        $i =       0;
        $b = array();

        foreach($array as $index => $value){

            if(is_string($index)){
                $index = (strpos($index, ' ') || strlen($index) == 0?  '"'. str_replace('"', '\\"', $index). '"' : $index);

            }elseif(is_numeric($index)){
                if(is_array($value)) {
                    $index = (string)$index;

                }elseif ($i == $index){
                    $index = '';
                    $i++;

                }elseif($i < $index){
                    $i = $index;
                    $i++;
                    $index = (string) $index;

                }else{
                    $index = (string) $index;

                }
            }

            if(is_array($value)) {
                $b[] = self::IR($value, $base.$index.'=');

            }else{

                $value = (string) $value;
                $value = (strpos($value, ' ')?  '"'. $value. '"' : $value);

                $b[] = $base. $index. (strlen($base. $index) > 0? '=': ''). $value;

            }

        }

        return implode(' ', $b);

    }

}