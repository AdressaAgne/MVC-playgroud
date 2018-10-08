<?php



Route::add('home/photo/', 'PageController@photo');

Route::add('home/', function(){
    return 'Home view';
});
Route::add('/', function(){
    dd(Route::list());
});

Route::add('home/video/', 'PageController@video');


Route::add('home/camera/{id}/{lol}', function($params) {
    return print_r($params, true);
});


Route::addError(404, function(){
    return "error 404, page does not exist";
});

