<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\User\Provider;
use Auth0\Laravel\Auth\User\Repository;
use Auth0\Laravel\Model\Stateful\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

uses()->group('auth', 'auth.user', 'auth.user.provider');

test('retrieveByToken() returns null when an incompatible guard token is used', function (): void {
    config([
        'auth.defaults.guard' => 'web',
        'auth.guards.testGuard' => null,
        'auth0.routes.home' => '/testing'
    ]);

    $route = '/' . uniqid();

    Route::get($route, function () {
        $provider = app('auth0.provider');
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
    $provider = app('auth0.provider');

    expect($provider->retrieveByToken('token', ''))
        ->toBeNull();

    expect($provider->retrieveByToken('token', []))
        ->toBeNull();
});

test('validateCredentials() always returns false', function (): void {
    $provider = app('auth0.provider');
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
