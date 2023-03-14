<?php

declare(strict_types=1);

use Auth0\Laravel\Contract\Model\Stateful\User;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

uses()->group('stateful', 'middleware', 'middleware.stateful', 'middleware.stateful.authenticate');

beforeEach(function (): void {
    $this->laravel = app('auth0');
    $this->guard = auth('testGuard');
    $this->sdk = $this->laravel->getSdk();
    $this->config = $this->sdk->configuration();
    $this->session = $this->config->getSessionStorage();
    $this->transient = $this->config->getTransientStorage();

    $this->secret = uniqid();

    $this->config->setDomain('my-domain.auth0.com');
    $this->config->setClientId('my_client_id');
    $this->config->setClientSecret($this->secret);
    $this->config->setCookieSecret('my_cookie_secret');
    $this->config->setStrategy(SdkConfiguration::STRATEGY_REGULAR);

    $this->templates['validSession'] = [
        'auth0_session_user' => ['sub' => 'hello|world'],
        'auth0_session_idToken' => uniqid(),
        'auth0_session_accessToken' => uniqid(),
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
        'auth.guards.testGuard' => null
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

    config($config = [
        'auth0.routes.login' => '/' . uniqid()
    ]);

    $this->get($route)
         ->assertRedirect($config['auth0.routes.login']);

    expect($this->guard)
         ->user()->toBeNull();
});

it('assigns a user', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate')->get($route, function () use ($route): string {
        return $route;
    });

    $this->withSession($this->templates['validSession'])
         ->get($route)
            ->assertStatus(Response::HTTP_OK)
            ->assertSee($route);

    expect($this->guard)
         ->user()->toBeInstanceOf(User::class);
});

it('assigns a user when using a configured scope matches', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate:read:admin')->get($route, function () use ($route): string {
        return $route;
    });

    $this->withSession($this->templates['validSession'])
         ->get($route)
            ->assertStatus(Response::HTTP_OK)
            ->assertSee($route);

    expect($this->guard)
         ->user()->toBeInstanceOf(User::class);
});

it('does not assign a user when a configured scope is not matched', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate:something:else')->get($route, function () use ($route): string {
        return $route;
    });

    $this->withSession($this->templates['validSession'])
         ->get($route)
         ->assertStatus(Response::HTTP_FORBIDDEN);

    expect($this->guard)
        ->user()->toBeNull();
});
