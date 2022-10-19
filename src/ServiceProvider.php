<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Auth\User\Provider;
use Auth0\Laravel\Auth\User\Repository;
use Auth0\Laravel\Http\Controller\Stateful\Callback;
use Auth0\Laravel\Http\Controller\Stateful\Login;
use Auth0\Laravel\Http\Controller\Stateful\Logout;
use Auth0\Laravel\Http\Middleware\Stateful\Authenticate;
use Auth0\Laravel\Http\Middleware\Stateful\AuthenticateOptional;
use Auth0\Laravel\Http\Middleware\Stateless\Authorize;
use Auth0\Laravel\Http\Middleware\Stateless\AuthorizeOptional;

final class ServiceProvider extends \Illuminate\Support\ServiceProvider implements \Auth0\Laravel\Contract\ServiceProvider
{
    public function provides()
    {
        return [Auth0::class, StateInstance::class, Repository::class, Guard::class, Provider::class, Authenticate::class, AuthenticateOptional::class, Authorize::class, AuthorizeOptional::class, Login::class, Logout::class, Callback::class];
    }

    public function register(): self
    {
        $this->mergeConfigFrom(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'config', 'auth0.php']), 'auth0');

        app()->singleton(Auth0::class, static fn (): Auth0 => new Auth0());
        app()->singleton(StateInstance::class, static fn (): StateInstance => new StateInstance());
        app()->singleton(Repository::class, static fn (): Repository => new Repository());
        app()->singleton(Guard::class, static fn (): Guard => new Guard());
        app()->singleton(Provider::class, static fn (): Provider => new Provider());
        app()->singleton(Authenticate::class, static fn (): Authenticate => new Authenticate());
        app()->singleton(AuthenticateOptional::class, static fn (): AuthenticateOptional => new AuthenticateOptional());
        app()->singleton(Authorize::class, static fn (): Authorize => new Authorize());
        app()->singleton(AuthorizeOptional::class, static fn (): AuthorizeOptional => new AuthorizeOptional());
        app()->singleton(Login::class, static fn (): Login => new Login());
        app()->singleton(Logout::class, static fn (): Logout => new Logout());
        app()->singleton(Callback::class, static fn (): Callback => new Callback());

        app()->singleton('auth0', static fn (): Auth0 => app(Auth0::class));

        app()->terminating(static function (): void {
            app()->instance(StateInstance::class, null);
        });

        return $this;
    }

    public function boot(\Illuminate\Routing\Router $router, \Illuminate\Auth\AuthManager $auth): self
    {
        $this->publishes([implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'config', 'auth0.php']) => config_path('auth0.php')], 'auth0-config');

        $auth->extend('auth0', static fn (): Guard => new Guard());
        $auth->provider('auth0', static fn (): Provider => new Provider());

        $router->aliasMiddleware('auth0.authenticate', Authenticate::class);
        $router->aliasMiddleware('auth0.authenticate.optional', AuthenticateOptional::class);
        $router->aliasMiddleware('auth0.authorize', Authorize::class);
        $router->aliasMiddleware('auth0.authorize.optional', AuthorizeOptional::class);

        return $this;
    }
}
