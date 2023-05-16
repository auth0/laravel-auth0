<?php

declare(strict_types=1);

use Auth0\Laravel\Auth0;
use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Bridges\{CacheBridge, CacheItemBridge, SessionBridge};
use Auth0\Laravel\Configuration;
use Auth0\Laravel\Controllers\{CallbackController, LoginController, LogoutController};
use Auth0\Laravel\Entities\CredentialEntity;
use Auth0\Laravel\Guards\{AuthenticationGuard, AuthorizationGuard};
use Auth0\Laravel\Middleware\{AuthenticateMiddleware, AuthenticateOptionalMiddleware, AuthenticatorMiddleware, AuthorizeMiddleware, AuthorizeOptionalMiddleware, AuthorizerMiddleware, GuardMiddleware};
use Auth0\Laravel\Service;
use Auth0\Laravel\ServiceProvider;
use Auth0\Laravel\UserProvider;
use Auth0\Laravel\UserRepository;
use Auth0\Laravel\Users\ImposterUser;
use Auth0\SDK\Token\Generator;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Support\Facades\Route;

uses()->group('ServiceProvider');

function setupGuardImpersonation(
    array $profile = [],
    array $scope = [],
    array $permissions = [],
): Authenticatable {
    Auth::shouldUse('legacyGuard');

    $imposter = new ImposterUser(array_merge([
        'sub' => uniqid(),
        'name' => uniqid(),
        'email' => uniqid() . '@example.com',
    ], $profile));

    Auth::guard()->setImpersonating(CredentialEntity::create(
        user: $imposter,
        idToken: (string) Generator::create((createRsaKeys())->private),
        accessToken: (string) Generator::create((createRsaKeys())->private),
        accessTokenScope: $scope,
        accessTokenDecoded: [
            'permissions' => $permissions,
        ],
    ));

    return $imposter;
}

function resetGuard(
    ?Authenticatable $imposter = null,
): void {
    Auth::shouldUse('web');

    if (null !== $imposter) {
        Auth::setUser($imposter);
    }

    config([
        'auth.defaults.guard' => 'web',
        'auth.guards.legacyGuard' => null,
    ]);
}

it('provides the expected classes', function (): void {
    $service = app(ServiceProvider::class, ['app' => $this->app]);

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

it('creates a Service singleton with `auth0` alias', function (): void {
    $singleton1 = $this->app->make('auth0');
    $singleton2 = $this->app->make(Service::class);

    expect($singleton1)
        ->toBeInstanceOf(Service::class);

    expect($singleton2)
        ->toBeInstanceOf(Service::class);

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

test('Gate::check(`scope`) returns true when a match hits', function (): void {
    setupGuardImpersonation(
        scope: [uniqid(), 'testScope', uniqid()],
    );

    expect(Gate::check('scope', 'testScope'))
        ->toBeTrue();
});

test('Gate::check(`scope`) returns false when a match misses', function (): void {
    setupGuardImpersonation(
        scope: [uniqid()],
    );

    expect(Gate::check('scope', 'testScope'))
        ->toBeFalse();
});

test('Gate::check(`scope`) returns false when an incompatible Guard is used', function (): void {
    $imposter = setupGuardImpersonation(
        scope: [uniqid(), 'testScope', uniqid()],
    );

    resetGuard($imposter);

    expect(Gate::check('scope', 'testScope'))
        ->toBeFalse();
});

test('Gate::check(`permission`) returns true when a match hits', function (): void {
    setupGuardImpersonation(
        permissions: [uniqid(), 'testPermission', uniqid()],
    );

    expect(Gate::check('permission', 'testPermission'))
        ->toBeTrue();
});

test('Gate::check(`permission`) returns false when a match misses', function (): void {
    setupGuardImpersonation(
        permissions: [uniqid()],
    );

    expect(Gate::check('permission', 'testPermission'))
        ->toBeFalse();
});

test('Gate::check(`permission`) returns false when an incompatible Guard is used', function (): void {
    $imposter = setupGuardImpersonation(
        permissions: [uniqid(), 'testPermission', uniqid()],
    );

    resetGuard($imposter);

    expect(Gate::check('permission', 'testPermission'))
        ->toBeFalse();
});

test('policies `can(scope)` middleware returns true when a match hits', function (): void {
    Route::get('/test', function () {
        return response()->json(['status' => 'OK']);
    })->can('scope:testScope');

    setupGuardImpersonation(
        scope: [uniqid(), 'testScope', uniqid()],
    );

    $this->getJson('/test')
         ->assertOK();
});

test('policies `can(scope)` middleware returns false when a match misses', function (): void {
    Route::get('/test', function () {
    })->can('scope:testScope');

    setupGuardImpersonation(
        scope: [uniqid()],
    );

    $this->getJson('/test')
         ->assertStatus(Response::HTTP_FORBIDDEN);
});

test('policies `can(scope)` middleware returns false when an incompatible Guard is used', function (): void {
    Route::get('/test', function () {
        return response()->json(['status' => 'OK']);
    })->can('scope:testScope');

    $imposter = setupGuardImpersonation(
        scope: [uniqid(), 'testScope', uniqid()],
    );

    resetGuard($imposter);

    $this->getJson('/test')
         ->assertStatus(Response::HTTP_FORBIDDEN);
});

test('policies `can(permission)` middleware returns true when a match hits', function (): void {
    Route::get('/test', function () {
        return response()->json(['status' => 'OK']);
    })->can('testing:123');

    setupGuardImpersonation(
        permissions: [uniqid(), 'testing:123', uniqid()],
    );

    $this->getJson('/test')
         ->assertOK();
});

test('policies `can(permission)` middleware returns false when a match misses', function (): void {
    Route::get('/test', function () {
        return response()->json(['status' => 'OK']);
    })->can('testing:123');

    setupGuardImpersonation(
        permissions: [uniqid(), 'testing:456', uniqid()],
    );

    $this->getJson('/test')
    ->assertStatus(Response::HTTP_FORBIDDEN);
});

test('policies `can(permission)` middleware returns false when an incompatible Guard is used', function (): void {
    Route::get('/test', function () {
        return response()->json(['status' => 'OK']);
    })->can('testing:123');

    $imposter = setupGuardImpersonation(
        permissions: [uniqid(), 'testing:123', uniqid()],
    );

    resetGuard($imposter);

    $this->getJson('/test')
    ->assertStatus(Response::HTTP_FORBIDDEN);
});

test('auth0.registerGuards === true registers guards', function (): void {
    config(['auth0.registerGuards' => true]);

    $service = app(ServiceProvider::class, ['app' => $this->app]);
    /**
     * @var ServiceProvider $service
     */
    $service->register();

    expect(config('auth.guards.auth0-session'))
        ->toBeArray()
        ->toHaveKey('driver', 'auth0.authenticator')
        ->toHaveKey('configuration', 'web')
        ->toHaveKey('provider', 'auth0-provider');

    expect(config('auth.guards.auth0-api'))
        ->toBeArray()
        ->toHaveKey('driver', 'auth0.authorizer')
        ->toHaveKey('configuration', 'api')
        ->toHaveKey('provider', 'auth0-provider');

    expect(config('auth.providers.auth0-provider'))
        ->toBeArray()
        ->toHaveKey('driver', 'auth0.provider')
        ->toHaveKey('repository', UserRepository::class);
});

test('auth0.registerGuards === true registers guards, but does not overwrite an existing auth.guards.auth0-session entry', function (): void {
    config([
        'auth0.registerGuards' => true,
        'auth.guards.auth0-session' => [
            'driver' => 'session',
            'provider' => 'users',
        ]
    ]);

    $service = app(ServiceProvider::class, ['app' => $this->app]);
    /**
     * @var ServiceProvider $service
     */
    $service->register();

    expect(config('auth.guards.auth0-session'))
        ->toBeArray()
        ->toHaveKey('driver', 'session')
        ->toHaveKey('provider', 'users')
        ->not()->toHaveKey('configuration');

    expect(config('auth.guards.auth0-api'))
        ->toBeArray()
        ->toHaveKey('driver', 'auth0.authorizer')
        ->toHaveKey('configuration', 'api')
        ->toHaveKey('provider', 'auth0-provider');

    expect(config('auth.providers.auth0-provider'))
        ->toBeArray()
        ->toHaveKey('driver', 'auth0.provider')
        ->toHaveKey('repository', UserRepository::class);
});

test('auth0.registerGuards === true registers guards, but does not overwrite an existing auth.guards.auth0-api entry', function (): void {
    config([
        'auth0.registerGuards' => true,
        'auth.guards.auth0-api' => [
            'driver' => 'api',
            'provider' => 'users',
        ]
    ]);

    $service = app(ServiceProvider::class, ['app' => $this->app]);
    /**
     * @var ServiceProvider $service
     */
    $service->register();

    expect(config('auth.guards.auth0-session'))
        ->toBeArray()
        ->toHaveKey('driver', 'auth0.authenticator')
        ->toHaveKey('configuration', 'web')
        ->toHaveKey('provider', 'auth0-provider');

    expect(config('auth.guards.auth0-api'))
        ->toBeArray()
        ->toHaveKey('driver', 'api')
        ->toHaveKey('provider', 'users')
        ->not()->toHaveKey('configuration');

    expect(config('auth.providers.auth0-provider'))
        ->toBeArray()
        ->toHaveKey('driver', 'auth0.provider')
        ->toHaveKey('repository', UserRepository::class);
});

test('auth0.registerGuards === true registers guards, but does not overwrite an existing auth.providers.auth0-provider entry', function (): void {
    config([
        'auth0.registerGuards' => true,
        'auth.providers.auth0-provider' => [
            'driver' => 'database',
            'repository' => 'users',
        ]
    ]);

    $service = app(ServiceProvider::class, ['app' => $this->app]);
    /**
     * @var ServiceProvider $service
     */
    $service->register();

    expect(config('auth.guards.auth0-session'))
        ->toBeArray()
        ->toHaveKey('driver', 'auth0.authenticator')
        ->toHaveKey('configuration', 'web')
        ->toHaveKey('provider', 'auth0-provider');

        expect(config('auth.guards.auth0-api'))
        ->toBeArray()
        ->toHaveKey('driver', 'auth0.authorizer')
        ->toHaveKey('configuration', 'api')
        ->toHaveKey('provider', 'auth0-provider');

    expect(config('auth.providers.auth0-provider'))
        ->toBeArray()
        ->toHaveKey('driver', 'database')
        ->toHaveKey('repository', 'users');
});

test('auth0.registerGuards === false does not register guards', function (): void {
    config(['auth0.registerGuards' => false]);

    $service = app(ServiceProvider::class, ['app' => $this->app]);
    /**
     * @var ServiceProvider $service
     */
    $service->register();

    expect(config('auth.guards.auth0-session'))
        ->toBeNull();

    expect(config('auth.guards.auth0-api'))
        ->toBeNull();

    expect(config('auth.providers.auth0-provider'))
        ->toBeNull();
});

test('auth0.registerGuards === null does not register guards', function (): void {
    $service = app(ServiceProvider::class, ['app' => $this->app]);
    /**
     * @var ServiceProvider $service
     */
    $service->register();

    expect(config('auth.guards.auth0-session'))
        ->toBeNull();

    expect(config('auth.guards.auth0-api'))
        ->toBeNull();

    expect(config('auth.providers.auth0-provider'))
        ->toBeNull();
});

test('auth0.registerMiddleware === true registers middleware', function (): void {
    config(['auth0.registerMiddleware' => true]);

    $service = app(ServiceProvider::class, ['app' => $this->app]);
    /**
     * @var ServiceProvider $service
     */
    $kernel = $service->registerMiddleware();
    /**
     * @var Kernel $kernel
     */

    $middleware = $kernel->getMiddlewareGroups();

    expect($middleware)
        ->toBeArray()
        ->toHaveKeys(['web', 'api']);

    expect($middleware['web'])
        ->toContain(AuthenticatorMiddleware::class);

    expect($middleware['api'])
        ->toContain(AuthorizerMiddleware::class);
});

test('auth0.registerMiddleware === false does not register middleware', function (): void {
    config('auth0.registerMiddleware', false);

    $service = app()->register(ServiceProvider::class, true);
    /**
     * @var ServiceProvider $service
     */
    $kernel = $service->registerMiddleware();
    /**
     * @var Kernel $kernel
     */

    $middleware = $kernel->getMiddlewareGroups();

    expect($middleware)
        ->toBeArray()
        ->toHaveKeys(['web', 'api']);

    expect($middleware['web'])
        ->not()->toContain(AuthenticatorMiddleware::class);

    expect($middleware['api'])
        ->not()->toContain(AuthorizerMiddleware::class);
});

test('auth0.registerMiddleware === null does not register middleware', function (): void {
    $service = app()->register(ServiceProvider::class, true);
    /**
     * @var ServiceProvider $service
     */
    $kernel = $service->registerMiddleware();
    /**
     * @var Kernel $kernel
     */

    $middleware = $kernel->getMiddlewareGroups();

    expect($middleware)
        ->toBeArray()
        ->toHaveKeys(['web', 'api']);

    expect($middleware['web'])
        ->not()->toContain(AuthenticatorMiddleware::class);

    expect($middleware['api'])
        ->not()->toContain(AuthorizerMiddleware::class);
});

test('auth0.registerAuthenticationRoutes === true registers routes', function (): void {
    config(['auth0.registerAuthenticationRoutes' => true]);

    $service = app(ServiceProvider::class, ['app' => $this->app]);
    /**
     * @var ServiceProvider $service
     */
    $service->registerRoutes();
    $routes = (array) Route::getRoutes()->get('GET');

    expect($routes)
        ->toHaveKeys(['login', 'logout', 'callback']);
});

test('auth0.registerAuthenticationRoutes === false does not register routes', function (): void {
    config(['auth0.registerAuthenticationRoutes' => false]);

    $service = app(ServiceProvider::class, ['app' => $this->app]);
    /**
     * @var ServiceProvider $service
     */
    $service->registerRoutes();
    $routes = (array) Route::getRoutes()->get('GET');

    expect($routes)
        ->not()->toHaveKeys(['login', 'logout', 'callback']);
});

test('auth0.registerAuthenticationRoutes === null does not register routes', function (): void {
    $service = app(ServiceProvider::class, ['app' => $this->app]);
    /**
     * @var ServiceProvider $service
     */
    $service->registerRoutes();
    $routes = (array) Route::getRoutes()->get('GET');

    expect($routes)
        ->not()->toHaveKeys(['login', 'logout', 'callback']);
});
