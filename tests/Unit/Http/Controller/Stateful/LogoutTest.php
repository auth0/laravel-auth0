<?php

declare(strict_types=1);

use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Support\Facades\Route;
use Auth0\Laravel\Http\Controller\Stateful\Logout;

uses()->group('stateful', 'controller', 'controller.stateful', 'controller.stateful.logout');

beforeEach(function (): void {
    $this->secret = uniqid();

    config([
        'auth0.strategy' => SdkConfiguration::STRATEGY_REGULAR,
        'auth0.domain' => uniqid() . '.auth0.com',
        'auth0.clientId' => uniqid(),
        'auth0.clientSecret' => $this->secret,
        'auth0.cookieSecret' => uniqid(),
        'auth0.routes.home' => '/' . uniqid(),
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();

    $this->validSession = [
        'auth0_session_user' => ['sub' => 'hello|world'],
        'auth0_session_idToken' => uniqid(),
        'auth0_session_accessToken' => uniqid(),
        'auth0_session_accessTokenScope' => [uniqid()],
        'auth0_session_accessTokenExpiration' => time() + 60,
    ];

    Route::get('/logout', Logout::class);
});

it('redirects to the home route if an incompatible guard is active', function (): void {
    config($config = [
        'auth.defaults.guard' => 'web',
        'auth.guards.legacyGuard' => null
    ]);

    $this->get('/logout')
         ->assertRedirect(config('auth0.routes.home'));
});

it('redirects to the home route when a user is not already logged in', function (): void {
    $this->get('/logout')
         ->assertRedirect(config('auth0.routes.home'));
});

it('redirects to the Auth0 logout endpoint', function (): void {
    $this->withSession($this->validSession)
         ->get('/logout')
            ->assertRedirectContains('/v2/logout');
});
