<?php

declare(strict_types=1);

use Auth0\Laravel\Users\UserContract;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;
use Auth0\SDK\Token\Generator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

uses()->group('stateful', 'middleware', 'middleware.stateless', 'middleware.stateless.authorize');

beforeEach(function (): void {
    $this->secret = uniqid();

    config([
        'auth0.AUTH0_CONFIG_VERSION' => 2,
        'auth0.guards.default.strategy' => SdkConfiguration::STRATEGY_API,
        'auth0.guards.default.domain' => uniqid() . '.auth0.com',
        'auth0.guards.default.clientId' => uniqid(),
        'auth0.guards.default.audience' => [uniqid()],
        'auth0.guards.default.clientSecret' => $this->secret,
        'auth0.guards.default.cookieSecret' => uniqid(),
        'auth0.guards.default.tokenAlgorithm' => Token::ALGO_HS256,
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();
});

it('does not assign a user when an incompatible guard is used', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize.optional')->get($route, function () use ($route): string {
        return json_encode(['status' => $route]);
    });

    config([
        'auth.defaults.guard' => 'web',
        'auth.guards.legacyGuard' => null
    ]);

    $this->getJson($route, ['Authorization' => 'Bearer ' . uniqid()])
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route]);

    expect($this->guard)
        ->user()->toBeNull();
});

it('does not assign a user when an invalid bearer token is provided', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize.optional')->get($route, function () use ($route): string {
        return json_encode(['status' => $route]);
    });

    $this->getJson($route, ['Authorization' => 'Bearer ' . uniqid()])
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route]);

    expect($this->guard)
        ->user()->toBeNull();
});

it('assigns a user', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize.optional')->get($route, function () use ($route): string {
        return json_encode(['status' => $route]);
    });

    $token = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => 'https://' . config('auth0.guards.default.domain') . '/',
        "sub" => "auth0|123456",
        "aud" => [
          "https://example.com/health-api",
          "https://my-domain.auth0.com/userinfo",
          config('auth0.guards.default.clientId')
        ],
        "azp" => config('auth0.guards.default.clientId'),
        "exp" => time() + 60,
        "iat" => time(),
        "scope" => "openid profile read:patients read:admin"
    ]);

    $this->getJson($route, ['Authorization' => 'Bearer ' . $token->toString()])
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route]);

    expect($this->guard)
        ->user()->toBeInstanceOf(UserContract::class);
});

it('assigns a user when using a configured scope matches', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize.optional:read:admin')->get($route, function () use ($route): string {
        return json_encode(['status' => $route]);
    });

    $token = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => 'https://' . config('auth0.guards.default.domain') . '/',
        "sub" => "auth0|123456",
        "aud" => [
          "https://example.com/health-api",
          "https://my-domain.auth0.com/userinfo",
          config('auth0.guards.default.clientId')
        ],
        "azp" => config('auth0.guards.default.clientId'),
        "exp" => time() + 60,
        "iat" => time(),
        "scope" => "openid profile read:patients read:admin"
    ]);

    $this->getJson($route, ['Authorization' => 'Bearer ' . $token->toString()])
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route]);

    expect($this->guard)
        ->user()->toBeInstanceOf(UserContract::class);
});

it('does not assign a user when a configured scope is not matched', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize.optional:something:else')->get($route, function () use ($route): string {
        return json_encode(['status' => $route]);
    });

    $token = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => 'https://' . config('auth0.guards.default.domain') . '/',
        "sub" => "auth0|123456",
        "aud" => [
          "https://example.com/health-api",
          "https://my-domain.auth0.com/userinfo",
          config('auth0.guards.default.clientId')
        ],
        "azp" => config('auth0.guards.default.clientId'),
        "exp" => time() + 60,
        "iat" => time(),
        "scope" => "openid profile read:patients read:admin"
    ]);

    $this->getJson($route, ['Authorization' => 'Bearer ' . $token->toString()])
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route]);

    expect($this->guard)
        ->user()->toBeNull();
});
