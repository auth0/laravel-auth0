<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/ping', array(function() {
    return "All good. You don't need to be authenticated to call this";
}));

Route::get('/secured/ping', array('before'=>'auth-jwt', function() {
    return "All good. You only get this message if you're authenticated";
}));
