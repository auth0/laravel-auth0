<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', ['as' => 'home', 'uses' => 'WelcomeController@index']);
Route::get('/auth/login', ['as' => 'login', 'uses' => 'WelcomeController@login']);
Route::get('/logout', ['as' => 'logout', 'uses' => 'WelcomeController@logout']);
Route::get('/auth0/callback', ['as' => 'logincallback', 'uses' => '\Auth0\Login\Auth0Controller@callback']);

Route::get('/dump', ['as' => 'dump', 'uses' => 'WelcomeController@dump', 'middleware' => 'auth']);

Route::get('/spa', ['as' => 'spa', 'uses' => 'WelcomeController@spa']);
Route::get('/api/ping', ['as' => 'api', 'uses' => 'WelcomeController@api', 'middleware' => 'auth0.jwt']);
