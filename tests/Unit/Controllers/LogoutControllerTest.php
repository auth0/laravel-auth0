<?php

declare(strict_types=1);

use Auth0\Laravel\Controllers\LogoutController;
use Auth0\Laravel\Exceptions\ControllerException;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token\Generator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

uses()->group('stateful', 'controller', 'controller.stateful', 'controller.stateful.logout');

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
        'auth0_session' => json_encode([
            'user' => ['sub' => 'hello|world'],
            'idToken' => (string) Generator::create((createRsaKeys())->private),
            'accessToken' => (string) Generator::create((createRsaKeys())->private),
            'accessTokenScope' => [uniqid()],
            'accessTokenExpiration' => time() + 60,
        ])
    ];

    Route::get('/logout', LogoutController::class);
});

it('redirects to the home route if an incompatible guard is active', function (): void {
    config($config = [
        'auth.defaults.guard' => 'web',
        'auth.guards.legacyGuard' => null
    ]);

    expect(function () {
        $this->withoutExceptionHandling()
             ->getJson('/logout')
             ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    })->toThrow(ControllerException::class);
});

it('redirects to the home route when a user is not already logged in', function (): void {
    $this->get('/logout')
         ->assertRedirect('/');
});

it('redirects to the Auth0 logout endpoint', function (): void {
    $this->withSession($this->validSession)
         ->get('/logout')
            ->assertRedirectContains('/v2/logout');
});
