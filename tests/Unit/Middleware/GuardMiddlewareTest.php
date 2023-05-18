<?php

declare(strict_types=1);

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

uses()->group('middleware', 'middleware.guard');

beforeEach(function (): void {
    $this->laravel = app('auth0');
});

it('assigns the guard for route handling', function (): void {
    $routeMiddlewareAssignedGuard = '/' . uniqid();
    $routeMiddlewareUnassignedGuard = '/' . uniqid();
    $routeUnspecifiedGuard = '/' . uniqid();

    $defaultGuardClass = 'Illuminate\Auth\SessionGuard';
    $sdkGuardClass = 'Auth0\Laravel\Auth\Guard';

    config(['auth.defaults.guard' => 'web']);

    Route::get($routeUnspecifiedGuard, function (): string {
        return get_class(auth()->guard());
    });

    Route::middleware('guard:legacyGuard')->get($routeMiddlewareAssignedGuard, function (): string {
        return get_class(auth()->guard());
    });

    Route::middleware('guard')->get($routeMiddlewareUnassignedGuard, function (): string {
        return get_class(auth()->guard());
    });

    $this->get($routeUnspecifiedGuard)
         ->assertStatus(Response::HTTP_OK)
         ->assertSee($defaultGuardClass);

    $this->get($routeMiddlewareAssignedGuard)
         ->assertStatus(Response::HTTP_OK)
         ->assertSee($sdkGuardClass);

    $this->get($routeUnspecifiedGuard)
         ->assertStatus(Response::HTTP_OK)
         ->assertSee($sdkGuardClass);

    $this->get($routeMiddlewareUnassignedGuard)
         ->assertStatus(Response::HTTP_OK)
         ->assertSee($defaultGuardClass);

    $this->get($routeUnspecifiedGuard)
         ->assertStatus(Response::HTTP_OK)
         ->assertSee($defaultGuardClass);
});

it('assigns the guard for route group handling', function (): void {
    $routeMiddlewareUnassignedGuard = '/' . uniqid();
    $routeUnspecifiedGuard = '/' . uniqid();

    $defaultGuardClass = 'Illuminate\Auth\SessionGuard';
    $sdkGuardClass = 'Auth0\Laravel\Auth\Guard';

    config(['auth.defaults.guard' => 'web']);

    Route::middleware('guard:legacyGuard')->group(function () use ($routeUnspecifiedGuard, $routeMiddlewareUnassignedGuard) {
        Route::get($routeUnspecifiedGuard, function (): string {
            return get_class(auth()->guard());
        });

        Route::middleware('guard')->get($routeMiddlewareUnassignedGuard, function (): string {
            return get_class(auth()->guard());
        });
    });

    $this->get($routeUnspecifiedGuard)
         ->assertStatus(Response::HTTP_OK)
         ->assertSee($sdkGuardClass);

    $this->get($routeMiddlewareUnassignedGuard)
         ->assertStatus(Response::HTTP_OK)
         ->assertSee($defaultGuardClass);

    $this->get($routeUnspecifiedGuard)
         ->assertStatus(Response::HTTP_OK)
         ->assertSee($sdkGuardClass);
});
