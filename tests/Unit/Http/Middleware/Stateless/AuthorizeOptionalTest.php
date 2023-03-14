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
    $this->config->setTokenAlgorithm(Token::ALGO_HS256);
    $this->config->setStrategy(SdkConfiguration::STRATEGY_API);
});

it('does not assign a user when an incompatible guard is used', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize.optional')->get($route, function () use ($route): string {
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
        "iss" => "https://my-domain.auth0.com/",
        "sub" => "auth0|123456",
        "aud" => [
          "https://example.com/health-api",
          "https://my-domain.auth0.com/userinfo",
          "my_client_id"
        ],
        "azp" => "my_client_id",
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

    Route::middleware('auth0.authorize.optional:read:admin')->get($route, function () use ($route): string {
        return json_encode(['status' => $route]);
    });

    $token = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => "https://my-domain.auth0.com/",
        "sub" => "auth0|123456",
        "aud" => [
          "https://example.com/health-api",
          "https://my-domain.auth0.com/userinfo",
          "my_client_id"
        ],
        "azp" => "my_client_id",
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

it('does not assign a user when a configured scope is not matched', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize.optional:something:else')->get($route, function () use ($route): string {
        return json_encode(['status' => $route]);
    });

    $token = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => "https://my-domain.auth0.com/",
        "sub" => "auth0|123456",
        "aud" => [
          "https://example.com/health-api",
          "https://my-domain.auth0.com/userinfo",
          "my_client_id"
        ],
        "azp" => "my_client_id",
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
