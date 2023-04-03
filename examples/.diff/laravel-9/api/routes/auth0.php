<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Response;

Auth::shouldUse('auth0-example-guard');

Route::middleware('auth0.authorize.optional')->get('/', function () {
    return Response::json([
        'description' => 'This endpoint is optionally authorized using a valid access token. Guest requests are also allowed.',
        'guard' => auth()->guard()->getName(),
        'guest' => auth()->guest(),
        'user' => auth()->user()->getAttributes(),
    ], 200, [], JSON_PRETTY_PRINT);
});

Route::middleware('auth0.authorize')->get('/private', function () {
    return Response::json([
        'description' => 'This endpoint is authorized with an access token.',
        'guard' => auth()->guard()->getName(),
        'guest' => auth()->guest(),
        'user' => auth()->user()->getAttributes(),
    ], 200, [], JSON_PRETTY_PRINT);
});

Route::middleware('auth0.authorize:example:scope')->get('/private-scope', function () {
    return Response::json([
        'description' => 'This endpoint is authorized with an access token and a matching scope.',
        'guard' => auth()->guard()->getName(),
        'guest' => auth()->guest(),
        'user' => auth()->user()->getAttributes(),
    ], 200, [], JSON_PRETTY_PRINT);
});

Route::middleware('auth0.authorize:something:else')->get('/private-another-scope', function () {
    return Response::json([
        'description' => 'You should never see this.',
        'guard' => auth()->guard()->getName(),
        'guest' => auth()->guest(),
        'user' => auth()->user()->getAttributes(),
    ], 200, [], JSON_PRETTY_PRINT);
});
