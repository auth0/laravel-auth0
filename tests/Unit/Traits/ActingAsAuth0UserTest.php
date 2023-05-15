<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Model\Imposter;
use Auth0\Laravel\Traits\ActingAsAuth0User;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

uses()->group('trait', 'impersonation', 'acting-as-auth0-user');

uses(ActingAsAuth0User::class);

beforeEach(function (): void {
    $this->secret = uniqid();
    $this->user = ['sub' => uniqid(), 'scope' => 'openid profile email read:messages'];
});

it('impersonates with other guards', function (): void {
    config([
        'auth0.default.strategy' => SdkConfiguration::STRATEGY_REGULAR,
        'auth0.default.domain' => uniqid() . '.auth0.com',
        'auth0.default.clientId' => uniqid(),
        'auth0.default.clientSecret' => $this->secret,
        'auth0.default.cookieSecret' => uniqid(),
        'auth0.default.tokenAlgorithm' => Token::ALGO_HS256,
        'auth.defaults.guard' => 'web',
        'auth.guards.legacyGuard' => null
    ]);

    $route = '/' . uniqid();

    Route::middleware('auth0.authorize')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $this->actingAsAuth0User(attributes: $this->user, source: Guard::SOURCE_SESSION)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK);

    expect(auth()->guard()->user())->not()->toBeNull();
});

it('impersonates a user against auth0.authenticate', function (): void {
    config([
        'auth0.default.strategy' => SdkConfiguration::STRATEGY_REGULAR,
        'auth0.default.domain' => uniqid() . '.auth0.com',
        'auth0.default.clientId' => uniqid(),
        'auth0.default.clientSecret' => $this->secret,
        'auth0.default.cookieSecret' => uniqid(),
        'auth0.default.tokenAlgorithm' => Token::ALGO_HS256,
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();

    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate')->get($route, function () use ($route) {
        return response()->json([
            'user' => auth()->user(),
            'status' => $route
        ]);
    });

    $this->actingAsAuth0User(attributes: $this->user, source: Guard::SOURCE_SESSION)
        ->getJson($route)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route])
        ->assertJsonFragment(['sub' => $this->user['sub']]);

    expect($this->guard)
        ->user()->toBeInstanceOf(\Auth0\Laravel\Model\Stateful\User::class);
});

it('impersonates a user against auth0.authenticate.optional', function (): void {
    config([
        'auth0.default.strategy' => SdkConfiguration::STRATEGY_REGULAR,
        'auth0.default.domain' => uniqid() . '.auth0.com',
        'auth0.default.clientId' => uniqid(),
        'auth0.default.clientSecret' => $this->secret,
        'auth0.default.cookieSecret' => uniqid(),
        'auth0.default.tokenAlgorithm' => Token::ALGO_HS256,
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();

    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate.optional')->get($route, function () use ($route) {
        return response()->json([
            'user' => auth()->user(),
            'status' => $route
        ]);
    });

    $this->actingAsAuth0User(attributes: $this->user, source: Guard::SOURCE_SESSION)
        ->getJson($route)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route])
        ->assertJsonFragment(['sub' => $this->user['sub']]);

    expect($this->guard)
        ->user()->toBeInstanceOf(\Auth0\Laravel\Model\Stateful\User::class);
});

it('impersonates a user against auth0.authenticate using a scope', function (): void {
    config([
        'auth0.default.strategy' => SdkConfiguration::STRATEGY_REGULAR,
        'auth0.default.domain' => uniqid() . '.auth0.com',
        'auth0.default.clientId' => uniqid(),
        'auth0.default.clientSecret' => $this->secret,
        'auth0.default.cookieSecret' => uniqid(),
        'auth0.default.tokenAlgorithm' => Token::ALGO_HS256,
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();

    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate:read:messages')->get($route, function () use ($route) {
        return response()->json([
            'user' => auth()->user(),
            'status' => $route
        ]);
    });

    $this->actingAsAuth0User(attributes: $this->user, source: Guard::SOURCE_SESSION)
        ->getJson($route)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route])
        ->assertJsonFragment(['sub' => $this->user['sub']]);

    expect($this->guard)
        ->user()->toBeInstanceOf(\Auth0\Laravel\Model\Stateful\User::class);
});

it('impersonates a user against auth0.authorize', function (): void {
    config([
        'auth0.default.strategy' => SdkConfiguration::STRATEGY_API,
        'auth0.default.domain' => uniqid() . '.auth0.com',
        'auth0.default.clientId' => uniqid(),
        'auth0.default.audience' => [uniqid()],
        'auth0.default.clientSecret' => $this->secret,
        'auth0.default.tokenAlgorithm' => Token::ALGO_HS256,
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();

    $route = '/' . uniqid();

    Route::middleware('auth0.authorize')->get($route, function () use ($route) {
        return response()->json([
            'user' => auth()->user(),
            'status' => $route
        ]);
    });

    $this->actingAsAuth0User(attributes: $this->user, source: Guard::SOURCE_TOKEN)
        ->getJson($route)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route])
        ->assertJsonFragment(['sub' => $this->user['sub']]);

    expect($this->guard)
        ->user()->toBeInstanceOf(\Auth0\Laravel\Model\Stateless\User::class);
});

it('impersonates a user against auth0.authorize.optional', function (): void {
    config([
        'auth0.default.strategy' => SdkConfiguration::STRATEGY_API,
        'auth0.default.domain' => uniqid() . '.auth0.com',
        'auth0.default.clientId' => uniqid(),
        'auth0.default.audience' => [uniqid()],
        'auth0.default.clientSecret' => $this->secret,
        'auth0.default.tokenAlgorithm' => Token::ALGO_HS256,
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();

    $route = '/' . uniqid();

    Route::middleware('auth0.authorize.optional')->get($route, function () use ($route) {
        return response()->json([
            'user' => auth()->user(),
            'status' => $route
        ]);
    });

    $this->actingAsAuth0User(attributes: $this->user, source: Guard::SOURCE_TOKEN)
        ->getJson($route)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route])
        ->assertJsonFragment(['sub' => $this->user['sub']]);

    expect($this->guard)
        ->user()->toBeInstanceOf(\Auth0\Laravel\Model\Stateless\User::class);
});

it('impersonates a user against auth0.authorize using a scope', function (): void {
    config([
        'auth0.default.strategy' => SdkConfiguration::STRATEGY_API,
        'auth0.default.domain' => uniqid() . '.auth0.com',
        'auth0.default.clientId' => uniqid(),
        'auth0.default.audience' => [uniqid()],
        'auth0.default.clientSecret' => $this->secret,
        'auth0.default.tokenAlgorithm' => Token::ALGO_HS256,
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();

    $route = '/' . uniqid();

    Route::middleware('auth0.authorize:read:messages')->get($route, function () use ($route) {
        return response()->json([
            'user' => auth()->user(),
            'status' => $route
        ]);
    });

    $this->actingAsAuth0User(attributes: $this->user, source: Guard::SOURCE_TOKEN)
        ->getJson($route)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route])
        ->assertJsonFragment(['sub' => $this->user['sub']]);

    expect($this->guard)
        ->user()->toBeInstanceOf(\Auth0\Laravel\Model\Stateless\User::class);
});
