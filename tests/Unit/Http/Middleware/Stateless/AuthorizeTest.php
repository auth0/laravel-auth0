<?php

declare(strict_types=1);

use Auth0\Laravel\Contract\Model\Stateless\User;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;
use Auth0\SDK\Token\Generator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

uses()->group('stateful', 'middleware', 'middleware.stateless', 'middleware.stateless.authorize');

beforeEach(function (): void {
    $this->secret = uniqid();

    config([
        'auth0.strategy' => SdkConfiguration::STRATEGY_API,
        'auth0.domain' => uniqid() . '.auth0.com',
        'auth0.clientId' => uniqid(),
        'auth0.audience' => [uniqid()],
        'auth0.clientSecret' => $this->secret,
        'auth0.cookieSecret' => uniqid(),
        'auth0.tokenAlgorithm' => Token::ALGO_HS256,
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('testGuard');
    $this->sdk = $this->laravel->getSdk();
});

it('does not assign a user when an incompatible guard is used', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize')->get($route, function () use ($route): string {
        return json_encode(['status' => $route]);
    });

    config([
        'auth.defaults.guard' => 'web',
        'auth.guards.testGuard' => null
    ]);

    $this->getJson($route, ['Authorization' => 'Bearer ' . uniqid()])
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route]);

    expect($this->guard)
        ->user()->toBeNull();
});

it('returns a 401 and does not assign a user when an invalid bearer token is provided', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize')->get($route, function () use ($route): string {
        return json_encode(['status' => $route]);
    });

    $this->getJson($route, ['Authorization' => 'Bearer ' . uniqid()])
        ->assertStatus(Response::HTTP_UNAUTHORIZED);

    expect($this->guard)
        ->user()->toBeNull();
});

it('assigns a user', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize')->get($route, function () use ($route): string {
        return json_encode(['status' => $route]);
    });

    $token = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => 'https://' . config('auth0.domain') . '/',
        "sub" => "auth0|123456",
        "aud" => [
          "https://example.com/health-api",
          "https://my-domain.auth0.com/userinfo",
          config('auth0.clientId')
        ],
        "azp" => config('auth0.clientId'),
        "exp" => time() + 60,
        "iat" => time(),
        "scope" => "openid profile read:patients read:admin"
    ]);

    $this->getJson($route, ['Authorization' => 'Bearer ' . $token->toString()])
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route]);

    expect($this->guard)
        ->user()->toBeInstanceOf(User::class);
});

it('assigns a user when using a configured scope matches', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize:read:admin')->get($route, function () use ($route): string {
        return json_encode(['status' => $route]);
    });

    $token = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => 'https://' . config('auth0.domain') . '/',
        "sub" => "auth0|123456",
        "aud" => [
          "https://example.com/health-api",
          "https://my-domain.auth0.com/userinfo",
          config('auth0.clientId')
        ],
        "azp" => config('auth0.clientId'),
        "exp" => time() + 60,
        "iat" => time(),
        "scope" => "openid profile read:patients read:admin"
    ]);

    $this->getJson($route, ['Authorization' => 'Bearer ' . $token->toString()])
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route]);

    expect($this->guard)
        ->user()->toBeInstanceOf(User::class);
});

it('returns a 403 and does not assign a user when a configured scope is not matched', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize:something:else')->get($route, function () use ($route): string {
        return json_encode(['status' => $route]);
    });

    $token = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => 'https://' . config('auth0.domain') . '/',
        "sub" => "auth0|123456",
        "aud" => [
          "https://example.com/health-api",
          "https://my-domain.auth0.com/userinfo",
          config('auth0.clientId')
        ],
        "azp" => config('auth0.clientId'),
        "exp" => time() + 60,
        "iat" => time(),
        "scope" => "openid profile read:patients read:admin"
    ]);

    $this->getJson($route, ['Authorization' => 'Bearer ' . $token->toString()])
        ->assertStatus(Response::HTTP_FORBIDDEN);

    expect($this->guard)
        ->user()->toBeNull();
});
