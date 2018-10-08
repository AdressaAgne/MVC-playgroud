<?php

spl_autoload_register(function($cls){
    $cls = str_replace('\\', '/', $cls);
    require_once(__DIR__."/$cls.php");
});


$alias = [
    'App\Container\Route'   => 'Route',
    'App\Container\Page'    => 'Page'
];

foreach($alias as $key => $value) {
    class_alias($key, $value);
}

/**
 * Basic functions
 */
require_once('App/functions.php');

/**
 * The Route setup
 */
require_once('App/routeSetup.php');


/**
 * Init
 */
$app = new App\App();