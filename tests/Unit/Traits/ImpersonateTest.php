<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Entities\Credential;
use Auth0\Laravel\Model\Stateless\User;
use Auth0\Laravel\Traits\Impersonate;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

uses()->group('trait', 'impersonate');

uses(Impersonate::class);

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

    $this->impersonating = Credential::create(
        user: new User(['sub' => uniqid()]),
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );
});

it('impersonates with other guards', function (): void {
    config([
        'auth.defaults.guard' => 'web',
        'auth.guards.testGuard' => null
    ]);

    $route = '/' . uniqid();

    Route::middleware('auth0.authorize')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $this->impersonate($this->impersonating)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK);

    expect($this->guard)
        ->user()->toBeNull();
});

it('impersonates a user against auth0.authenticate', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $this->impersonate($this->impersonating, Guard::SOURCE_SESSION)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($this->impersonating->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(User::class)
        ->toBe($this->impersonating->getUser());
});

it('impersonates a user against auth0.authenticate.optional', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate.optional')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $this->impersonate($this->impersonating, Guard::SOURCE_SESSION)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($this->impersonating->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(User::class)
        ->toBe($this->impersonating->getUser());
});

it('impersonates a user against auth0.authenticate using a scope', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate:read:messages')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $this->impersonate($this->impersonating, Guard::SOURCE_SESSION)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($this->impersonating->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(User::class)
        ->toBe($this->impersonating->getUser());
});

it('impersonates a user against auth0.authorize', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $this->impersonate($this->impersonating, Guard::SOURCE_TOKEN)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($this->impersonating->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(User::class)
        ->toBe($this->impersonating->getUser());
});

it('impersonates a user against auth0.authorize.optional', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize.optional')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $this->impersonate($this->impersonating, Guard::SOURCE_TOKEN)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($this->impersonating->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(User::class)
        ->toBe($this->impersonating->getUser());
});

it('impersonates a user against auth0.authorize using a scope', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize:read:messages')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $this->impersonate($this->impersonating, Guard::SOURCE_TOKEN)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($this->impersonating->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(User::class)
        ->toBe($this->impersonating->getUser());
});
