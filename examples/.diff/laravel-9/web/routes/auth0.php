<?php

use Illuminate\Support\Facades\Route;
use Auth0\Laravel\Http\Controller\Stateful\{Login, Logout, Callback};

Route::get('/login', Login::class)->name('login');
Route::get('/logout', Logout::class)->name('logout');
Route::get('/callback', Callback::class)->name('callback');
