<?php

namespace App\Container;

class Page {

    private $callback;
    private $vars;
    private $name;
    private $children = [];

    function __construct($path, $callback){
        $path = is_array($path) ? $path : explode('/', trim($path, '/'));
        $this->name = array_shift($path);

        $this->add($path, $callback);
    }

    /**
     * get a child by path
     */
    public function get(Array $path){
        $name = array_shift($path);
        
        if(!$this->hasChild($name) && !empty($this->vars)){
            array_unshift($path, $name);
            $vars = [];

            foreach($this->vars as $key => $var){
                if(!isset($path[$key])) continue;
                $vars[$var] = $path[$key];
            }

            $this->filledVars = (object) $vars;

            return $this;
        }
       
        if(empty($path)){
            if($this->hasChild($name)) return $this->getChild($name);

            return $this->sendError('404');
        }

        return $this->children[$name]->get($path);

    }

    /**
     * check if a child page exists
     */
    public function childExist($child) {
        return isset($this->children[$child]);
    }

    public function hasChild($child) {
        return $this->childExist($child);
    }

    public function hasChildren(){
        return count($this->children) > 0;
    }

    /**
     * get a child page
     */
    public function getChild($child) {
        if($this->childExist($child)) {
            return $this->children[$child];
        }

        return false;
    }

    /**
     * Add a new child
     */
    public function add($path, $callback) {
        $path = is_array($path) ? $path : explode('/', trim($path, '/'));
        $name = isset($path[0]) ? $path[0] : "";

        // if there is no more path, or vars has started this is the end point
        if(empty($path) || preg_match('/\\{(.+)\\}/um', $name)) {

            $this->callback = $this->parseCallback($callback);
            
            $this->vars = array_map(function($item){
               return preg_replace('/\\{(.+)\\}/um', '$1', $item);
            }, $path);

            $this->_callback = $callback;

        } else {
            
            if($this->childExist($name)){
                array_shift($path);
                $this->getChild($name)->add($path, $callback);

            } else {
                $this->children[$name] = new Page($path, $callback);
            }

        }
    }

    /**
     * @return String;
     */
    public function __toString(){
        $vars = isset($this->filledVars) ? $this->filledVars : [];

        return call_user_func($this->callback, $vars);
    }

    /**
     * Print the page's tree structure
     */
    public function printTree($arr = []){
        
        if(!empty($this->vars)){
            $arr['_vars'] = $this->vars;
        }

        if(!$this->hasChildren()) {
            if(empty($arr)) return 'Page';
            return $arr;
        }

        foreach($this->children as $child) {
            $arr[$child->getName()] = $child->printTree();
            
        }

        return $arr;
    }

    /**
     * get page name
     */
    public function getName(){
        return $this->name;
    }

    /**
     * Send an error
     */
    private function sendError($code = '404') {
        return Route::getError($code);
    }


    /**
     * parse the callback string
     */
    private function parseCallback($cb){

        if(is_callable($cb)) {
            return $cb;
        }

        if(is_string($cb)) {
            $parts = explode('@', $cb);
            $parts[0] = 'App\Controllers\\'.$parts[0];
            $parts[0] = new $parts[0];
            return $parts;
        }

        return false;
    }

}