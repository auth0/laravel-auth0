<?php

declare(strict_types=1);

use Auth0\Laravel\Controllers\LoginController;
use Auth0\Laravel\Exceptions\ControllerException;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Support\Facades\Route;
use Auth0\Laravel\Http\Controller\Stateful\Login;
use Auth0\SDK\Token\Generator;
use Illuminate\Http\Response;

uses()->group('stateful', 'controller', 'controller.stateful', 'controller.stateful.login');

beforeEach(function (): void {
    $this->secret = uniqid();

    config([
        'auth0.AUTH0_CONFIG_VERSION' => 2,
        'auth0.guards.default.strategy' => SdkConfiguration::STRATEGY_REGULAR,
        'auth0.guards.default.domain' => uniqid() . '.auth0.com',
        'auth0.guards.default.clientId' => uniqid(),
        'auth0.guards.default.clientSecret' => $this->secret,
        'auth0.guards.default.cookieSecret' => uniqid(),
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();

    $this->validSession = [
        'auth0_session_user' => ['sub' => 'hello|world'],
        'auth0_session_idToken' => (string) Generator::create((createRsaKeys())->private),
        'auth0_session_accessToken' => (string) Generator::create((createRsaKeys())->private),
        'auth0_session_accessTokenScope' => [uniqid()],
        'auth0_session_accessTokenExpiration' => time() + 60,
    ];

    Route::get('/login', LoginController::class);
});

it('redirects to the home route if an incompatible guard is active', function (): void {
    config($config = [
        'auth.defaults.guard' => 'web',
        'auth.guards.legacyGuard' => null,
    ]);

    expect(function () {
        $this->withoutExceptionHandling()
             ->getJson('/login')
             ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    })->toThrow(ControllerException::class);
});

it('redirects to the home route when a user is already logged in', function (): void {
    $this->withSession($this->validSession)
         ->get('/login')
            ->assertRedirect('/');
});

it('redirects to the Universal Login Page', function (): void {
    $this->get('/login')
         ->assertRedirectContains('/authorize');
});
