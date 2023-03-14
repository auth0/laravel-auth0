<?php

declare(strict_types=1);

use Auth0\Laravel\Model\Imposter;
use Auth0\Laravel\Traits\ActingAsAuth0User;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

uses()->group('trait', 'impersonation');

uses(ActingAsAuth0User::class);

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

    $this->user = ['sub' => uniqid(), 'scope' => 'openid profile email read:messages'];
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

    $this->actingAsAuth0User($this->user, null)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK);

    expect($this->guard)
        ->user()->toBeNull();
});

it('impersonates a user against auth0.authenticate', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate')->get($route, function () use ($route) {
        return response()->json([
            'user' => auth()->user(),
            'status' => $route
        ]);
    });

    $this->actingAsAuth0User($this->user, null)
        ->getJson($route)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route])
        ->assertJsonFragment(['sub' => $this->user['sub']]);

    expect($this->guard)
        ->user()->toBeInstanceOf(Imposter::class);
});

it('impersonates a user against auth0.authenticate.optional', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate.optional')->get($route, function () use ($route) {
        return response()->json([
            'user' => auth()->user(),
            'status' => $route
        ]);
    });

    $this->actingAsAuth0User($this->user, null)
        ->getJson($route)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route])
        ->assertJsonFragment(['sub' => $this->user['sub']]);

    expect($this->guard)
        ->user()->toBeInstanceOf(Imposter::class);
});

it('impersonates a user against auth0.authenticate using a scope', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate:read:messages')->get($route, function () use ($route) {
        return response()->json([
            'user' => auth()->user(),
            'status' => $route
        ]);
    });

    $this->actingAsAuth0User($this->user, null)
        ->getJson($route)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route])
        ->assertJsonFragment(['sub' => $this->user['sub']]);

    expect($this->guard)
        ->user()->toBeInstanceOf(Imposter::class);
});

it('impersonates a user against auth0.authorize', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize')->get($route, function () use ($route) {
        return response()->json([
            'user' => auth()->user(),
            'status' => $route
        ]);
    });

    $this->actingAsAuth0User($this->user, null)
        ->getJson($route)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route])
        ->assertJsonFragment(['sub' => $this->user['sub']]);

    expect($this->guard)
        ->user()->toBeInstanceOf(Imposter::class);
});

it('impersonates a user against auth0.authorize.optional', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize.optional')->get($route, function () use ($route) {
        return response()->json([
            'user' => auth()->user(),
            'status' => $route
        ]);
    });

    $this->actingAsAuth0User($this->user, null)
        ->getJson($route)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route])
        ->assertJsonFragment(['sub' => $this->user['sub']]);

    expect($this->guard)
        ->user()->toBeInstanceOf(Imposter::class);
});

it('impersonates a user against auth0.authorize using a scope', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize:read:messages')->get($route, function () use ($route) {
        return response()->json([
            'user' => auth()->user(),
            'status' => $route
        ]);
    });

    $this->actingAsAuth0User($this->user, null)
        ->getJson($route)
        ->assertStatus(Response::HTTP_OK)
        ->assertJson(['status' => $route])
        ->assertJsonFragment(['sub' => $this->user['sub']]);

    expect($this->guard)
        ->user()->toBeInstanceOf(Imposter::class);
});
