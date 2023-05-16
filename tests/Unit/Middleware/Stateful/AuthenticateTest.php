<?php

declare(strict_types=1);

use Auth0\Laravel\Users\UserContract;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token\Generator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

uses()->group('stateful', 'middleware', 'middleware.stateful', 'middleware.stateful.authenticate');

beforeEach(function (): void {
    $this->secret = uniqid();

    config([
        'auth0.default.strategy' => SdkConfiguration::STRATEGY_REGULAR,
        'auth0.default.domain' => uniqid() . '.auth0.com',
        'auth0.default.clientId' => uniqid(),
        'auth0.default.clientSecret' => $this->secret,
        'auth0.default.cookieSecret' => uniqid(),
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();

    $this->validSession = [
        'auth0_session_user' => ['sub' => 'hello|world'],
        'auth0_session_idToken' => (string) Generator::create((createRsaKeys())->private),
        'auth0_session_accessToken' => (string) Generator::create((createRsaKeys())->private),
        'auth0_session_accessTokenScope' => [uniqid(), 'read:admin'],
        'auth0_session_accessTokenExpiration' => time() + 60,
    ];
});

it('does not assign a user when an incompatible guard is used', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate')->get($route, function () use ($route): string {
        return $route;
    });

    config($config = [
        'auth.defaults.guard' => 'web',
        'auth.guards.legacyGuard' => null
    ]);

    $this->get($route)
         ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);

    expect($this->guard)
         ->user()->toBeNull();
});

it('redirects to login route if a visitor does not have a session', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate')->get($route, function () use ($route): string {
        return $route;
    });

    $this->get($route)
         ->assertRedirect('/login');

    expect(redirect()->getIntendedUrl())
        ->toEqual('http://localhost' . $route);

    expect($this->guard)
         ->user()->toBeNull();
});

it('assigns a user', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate')->get($route, function () use ($route): string {
        return $route;
    });

    $this->withSession($this->validSession)
         ->get($route)
            ->assertStatus(Response::HTTP_OK)
            ->assertSee($route);

    expect($this->guard)
         ->user()->toBeInstanceOf(UserContract::class);
});

it('assigns a user when using a configured scope matches', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate:read:admin')->get($route, function () use ($route): string {
        return $route;
    });

    $this->withSession($this->validSession)
         ->get($route)
            ->assertStatus(Response::HTTP_OK)
            ->assertSee($route);

    expect($this->guard)
         ->user()->toBeInstanceOf(UserContract::class);
});

it('does not assign a user when a configured scope is not matched', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate:something:else')->get($route, function () use ($route): string {
        return $route;
    });

    $this->withSession($this->validSession)
         ->get($route)
         ->assertStatus(Response::HTTP_FORBIDDEN);

    expect($this->guard)
        ->user()->toBeNull();
});
