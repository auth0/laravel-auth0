<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Auth\User\Provider;
use Auth0\Laravel\Auth\User\Repository;
use Auth0\Laravel\Auth0;
use Auth0\Laravel\Auth\Guards\{SessionGuard, TokenGuard};
use Auth0\Laravel\Http\Controller\Stateful\{Callback, Login, Logout};
use Auth0\Laravel\Http\Middleware\Stateful\{Authenticate, AuthenticateOptional};
use Auth0\Laravel\Http\Middleware\Stateless\{Authorize, AuthorizeOptional};
use Auth0\Laravel\Http\Middleware\Guard as ShouldUseMiddleware;
use Auth0\Laravel\Store\LaravelSession;
use Illuminate\Support\Facades\Auth;

uses()->group('service-provider');

it('provides the expected classes', function (): void {
    $service = app(\Auth0\Laravel\ServiceProvider::class, ['app' => $this->app]);

    expect($service->provides())
        ->toBe([
            Auth0::class,
            Authenticate::class,
            AuthenticateOptional::class,
            Authorize::class,
            AuthorizeOptional::class,
            Callback::class,
            Guard::class,
            LaravelSession::class,
            Login::class,
            Logout::class,
            Provider::class,
            Repository::class,
            SessionGuard::class,
            ShouldUseMiddleware::class,
            TokenGuard::class,
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

it('creates a Repository singleton', function (): void {
    $singleton1 = $this->app->make('auth0.repository');
    $singleton2 = $this->app->make(Repository::class);

    expect($singleton1)
        ->toBeInstanceOf(Repository::class);

    expect($singleton2)
        ->toBeInstanceOf(Repository::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('does NOT a Provider singleton', function (): void {
    $singleton1 = Auth::createUserProvider('testProvider');
    $singleton2 = $this->app->make(Provider::class);

    expect($singleton1)
        ->toBeInstanceOf(Provider::class);

    expect($singleton2)
        ->toBeInstanceOf(Provider::class);

    expect($singleton1)
        ->not()->toBe($singleton2);
});

it('creates a Authenticate singleton', function (): void {
    $singleton1 = $this->app->make(Authenticate::class);
    $singleton2 = $this->app->make(Authenticate::class);

    expect($singleton1)
        ->toBeInstanceOf(Authenticate::class);

    expect($singleton2)
        ->toBeInstanceOf(Authenticate::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('creates a AuthenticateOptional singleton', function (): void {
    $singleton1 = $this->app->make(AuthenticateOptional::class);
    $singleton2 = $this->app->make(AuthenticateOptional::class);

    expect($singleton1)
        ->toBeInstanceOf(AuthenticateOptional::class);

    expect($singleton2)
        ->toBeInstanceOf(AuthenticateOptional::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('creates a Authorize singleton', function (): void {
    $singleton1 = $this->app->make(Authorize::class);
    $singleton2 = $this->app->make(Authorize::class);

    expect($singleton1)
        ->toBeInstanceOf(Authorize::class);

    expect($singleton2)
        ->toBeInstanceOf(Authorize::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('creates a AuthorizeOptional singleton', function (): void {
    $singleton1 = $this->app->make(AuthorizeOptional::class);
    $singleton2 = $this->app->make(AuthorizeOptional::class);

    expect($singleton1)
        ->toBeInstanceOf(AuthorizeOptional::class);

    expect($singleton2)
        ->toBeInstanceOf(AuthorizeOptional::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('creates a Login singleton', function (): void {
    $singleton1 = $this->app->make(Login::class);
    $singleton2 = $this->app->make(Login::class);

    expect($singleton1)
        ->toBeInstanceOf(Login::class);

    expect($singleton2)
        ->toBeInstanceOf(Login::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('creates a Logout singleton', function (): void {
    $singleton1 = $this->app->make(Logout::class);
    $singleton2 = $this->app->make(Logout::class);

    expect($singleton1)
        ->toBeInstanceOf(Logout::class);

    expect($singleton2)
        ->toBeInstanceOf(Logout::class);

    expect($singleton1)
        ->toBe($singleton2);
});

it('creates a Callback singleton', function (): void {
    $singleton1 = $this->app->make(Callback::class);
    $singleton2 = $this->app->make(Callback::class);

    expect($singleton1)
        ->toBeInstanceOf(Callback::class);

    expect($singleton2)
        ->toBeInstanceOf(Callback::class);

    expect($singleton1)
        ->toBe($singleton2);
});
