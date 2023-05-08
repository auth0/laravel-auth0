<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\User\Provider;
use Auth0\Laravel\Auth\User\Repository;
use Auth0\Laravel\Model\Stateful\User;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

uses()->group('auth', 'auth.user', 'auth.user.provider');

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
});

test('retrieveByToken() returns null when an incompatible guard token is used', function (): void {
    config([
        'auth.defaults.guard' => 'web',
        'auth.guards.legacyGuard' => null
    ]);

    $route = '/' . uniqid();

    Route::get($route, function () {
        $provider = Auth::createUserProvider('testProvider');
        $credential = $provider->retrieveByToken('token', '');

        if (null === $credential) {
            return response()->json(['status' => 'OK']);
        }

        abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
    });

    $this->getJson($route)
         ->assertOK();
});

test('retrieveByToken() returns null when an invalid token is provided', function (): void {
    $provider = Auth::createUserProvider('testProvider');

    expect($provider->retrieveByToken('token', ''))
        ->toBeNull();

    expect($provider->retrieveByToken('token', []))
        ->toBeNull();
});

test('validateCredentials() always returns false', function (): void {
    $provider = Auth::createUserProvider('testProvider');
    $user = new User();

    expect($provider->validateCredentials($user, []))
        ->toBeFalse();
});

test('getRepository() throws an error when an non-existent repository provider is set', function (): void {
    $provider = new Provider(['model' => 'MISSING']);
    $provider->getRepository();
})->throws(BindingResolutionException::class);

test('getRepository() throws an error when an invalid repository provider is set', function (): void {
    $provider = new Provider(['model' => ['ARRAY']]);
    $provider->getRepository();
})->throws(BindingResolutionException::class);

test('setRepository() sets the repository model', function (): void {
    $provider = new Provider(['model' => uniqid()]);
    $repository = new Repository();
    $provider->setRepository($repository::class);

    expect($provider->getRepository())
        ->toBeInstanceOf($repository::class);
});

test('setRepository() with the same repository identifier uses the cached repository instance', function (): void {
    $provider = new Provider(['model' => 'MISSING']);
    $repository = new Repository();

    $provider->setRepository($repository::class);

    expect($provider->getRepository())
        ->toBeInstanceOf($repository::class);

    $provider->setRepository($repository::class);

    expect($provider->getRepository())
        ->toBeInstanceOf($repository::class);
});
