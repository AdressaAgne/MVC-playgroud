<?php

namespace App;

use Route;

class App {

    function __construct(){

        $value = Route::parseURL( isset($_SERVER['REDIRECT_URL']) ? $_SERVER['REDIRECT_URL'] : ['_index'] );

        if(is_array($value)) {
            dd($value);
        } else {
            echo $value;
        }

    }

}