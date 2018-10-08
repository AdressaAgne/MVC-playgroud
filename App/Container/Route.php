<?php

namespace App\Container;

use Page;

class Route {

    private static $routes = null;

    /**
     *      
     */
    public static function add(String $path, $callable) {
        if(is_null(self::$routes)) self::$routes = new Page('_void', 'MainController@index');

        if($path == '/') $path = '_index';

        self::$routes->add($path, $callable);
    }

    /**
     * Add an error
     */
    public static function addError(int $code, $callable) {
        Route::add("$code", $callable);
    }

    /**
     * parse the url
     */
    public static function parseURL($url, $arr = null) {
        $url = is_array($url) ? $url : explode('/', trim($url, '/'));
        
        return self::$routes->get($url);
    }

    /**
     * get error
     */
    public static function getError($code){
        if(self::$routes->childExist("$code")) {
            return self::$routes->getChild("$code");
        }
        
        dd("Please set up a $code page");
    }


    /**
     * get full tree structure
     */
    public function __toString() {
        return [self::$routes->getName() => self::$routes->printTree()];
    }
    
    public static function list(){
        return [self::$routes->getName() => self::$routes->printTree()];
    }

}