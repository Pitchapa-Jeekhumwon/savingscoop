<?php

class Debug
{
    public function __construct()
    {

    }

    public static function check( $var, $key = '' , $val = '', $method = 'array'){
        if($key == $val){
            if($method == 'array') {
                self::_print_r($var);
            }else if($method == 'var_dump'){
                self::_var_dump($var);
            }else{
                self::_echo($var);
            }
        }
    }

    public static function _print_r($array = array()){
        echo "<pre>";
        print_r($array);
        echo "</pre>";
    }

    public static function _var_dump(array $any){
        echo "<pre>";
        var_dump($any);
        echo "</pre>";
    }

    public static function _echo($data){
        echo "<pre>";
        var_dump($data);
        echo "</pre>";
    }
}