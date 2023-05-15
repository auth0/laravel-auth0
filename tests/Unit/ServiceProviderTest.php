<?php

declare(strict_types=1);

use Auth0\Laravel\Auth0;
use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Bridges\{CacheBridge, CacheItemBridge, SessionBridge};
use Auth0\Laravel\Configuration;
use Auth0\Laravel\Controllers\{CallbackController, LoginController, LogoutController};
use Auth0\Laravel\Guards\{AuthenticationGuard, AuthorizationGuard};
use Auth0\Laravel\Middleware\{AuthenticateMiddleware, AuthenticateOptionalMiddleware, AuthenticatorMiddleware, AuthorizeMiddleware, AuthorizeOptionalMiddleware, AuthorizerMiddleware, GuardMiddleware};
use Auth0\Laravel\Service;
use Auth0\Laravel\UserProvider;
use Auth0\Laravel\UserRepository;
use Illuminate\Support\Facades\Auth;

uses()->group('ServiceProvider');

it('provides the expected classes', function (): void {
    $service = app(\Auth0\Laravel\ServiceProvider::class, ['app' => $this->app]);

    expect($service->provides())
        ->toBe([
            Auth0::class,
            AuthenticateMiddleware::class,
            AuthenticateOptionalMiddleware::class,
            AuthenticationGuard::class,
            AuthenticatorMiddleware::class,
            AuthorizationGuard::class,
            AuthorizeMiddleware::class,
            AuthorizeOptionalMiddleware::class,
            AuthorizerMiddleware::class,
            CacheBridge::class,
            CacheItemBridge::class,
            CallbackController::class,
            Configuration::class,
            Guard::class,
            GuardMiddleware::class,
            LoginController::class,
            LogoutController::class,
            Service::class,
            SessionBridge::class,
            UserProvider::class,
            UserRepository::class,
        ]);
});

it('creates a Auth0 singleton', function (): void {
    $singleton1 = $this->app->make('auth0');
    $singleton2 = $this->app->make(Auth0::class);

    expect($singleton1)
        ->toBeInstanceOf(Auth0::class);

    expect($singleton2)
        ->toBeInstanceOf(Auth0::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('does NOT create a Guard singleton', function (): void {
    $singleton1 = auth()->guard('legacyGuard');
    $singleton2 = $this->app->make(Guard::class);

    expect($singleton1)
        ->toBeInstanceOf(Guard::class);

    expect($singleton2)
        ->toBeInstanceOf(Guard::class);

    expect($singleton1)
        ->not()->toBe($singleton2);
});

it('creates a UserRepository singleton', function (): void {
    $singleton1 = $this->app->make('auth0.repository');
    $singleton2 = $this->app->make(UserRepository::class);

    expect($singleton1)
        ->toBeInstanceOf(UserRepository::class);

    expect($singleton2)
        ->toBeInstanceOf(UserRepository::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('does NOT a Provider singleton', function (): void {
    $singleton1 = Auth::createUserProvider('testProvider');
    $singleton2 = $this->app->make(UserProvider::class);

    expect($singleton1)
        ->toBeInstanceOf(UserProvider::class);

    expect($singleton2)
        ->toBeInstanceOf(UserProvider::class);

    expect($singleton1)
        ->not()->toBe($singleton2);
});

it('creates a AuthenticateMiddleware singleton', function (): void {
    $singleton1 = $this->app->make(AuthenticateMiddleware::class);
    $singleton2 = $this->app->make(AuthenticateMiddleware::class);

    expect($singleton1)
        ->toBeInstanceOf(AuthenticateMiddleware::class);

    expect($singleton2)
        ->toBeInstanceOf(AuthenticateMiddleware::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('creates a AuthenticateOptionalMiddleware singleton', function (): void {
    $singleton1 = $this->app->make(AuthenticateOptionalMiddleware::class);
    $singleton2 = $this->app->make(AuthenticateOptionalMiddleware::class);

    expect($singleton1)
        ->toBeInstanceOf(AuthenticateOptionalMiddleware::class);

    expect($singleton2)
        ->toBeInstanceOf(AuthenticateOptionalMiddleware::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('creates a AuthorizeMiddleware singleton', function (): void {
    $singleton1 = $this->app->make(AuthorizeMiddleware::class);
    $singleton2 = $this->app->make(AuthorizeMiddleware::class);

    expect($singleton1)
        ->toBeInstanceOf(AuthorizeMiddleware::class);

    expect($singleton2)
        ->toBeInstanceOf(AuthorizeMiddleware::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('creates a AuthorizeOptionalMiddleware singleton', function (): void {
    $singleton1 = $this->app->make(AuthorizeOptionalMiddleware::class);
    $singleton2 = $this->app->make(AuthorizeOptionalMiddleware::class);

    expect($singleton1)
        ->toBeInstanceOf(AuthorizeOptionalMiddleware::class);

    expect($singleton2)
        ->toBeInstanceOf(AuthorizeOptionalMiddleware::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('creates a LoginController singleton', function (): void {
    $singleton1 = $this->app->make(LoginController::class);
    $singleton2 = $this->app->make(LoginController::class);

    expect($singleton1)
        ->toBeInstanceOf(LoginController::class);

    expect($singleton2)
        ->toBeInstanceOf(LoginController::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('creates a LogoutController singleton', function (): void {
    $singleton1 = $this->app->make(LogoutController::class);
    $singleton2 = $this->app->make(LogoutController::class);

    expect($singleton1)
        ->toBeInstanceOf(LogoutController::class);

    expect($singleton2)
        ->toBeInstanceOf(LogoutController::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('creates a CallbackController singleton', function (): void {
    $singleton1 = $this->app->make(CallbackController::class);
    $singleton2 = $this->app->make(CallbackController::class);

    expect($singleton1)
        ->toBeInstanceOf(CallbackController::class);

    expect($singleton2)
        ->toBeInstanceOf(CallbackController::class);

    expect($singleton1)
        ->toBe($singleton2);
});
