<?php

namespace App\Override;
/**
 * PSR-4 Autoloader
 */
class Autoloader {
    
    public static function register(){
        spl_autoload_register(array(__CLASS__,'autoload'));
    }

    public static function autoload($class){
        if(strpos($class, 'App\\Override\\') === 0 ){
            $class = str_replace('App\\Override\\', '', $class);
            $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
            $file = __DIR__. DIRECTORY_SEPARATOR .$class.'.php'; 
            if(file_exists($file)){
                require $file;  
            }
        }
    }
}
