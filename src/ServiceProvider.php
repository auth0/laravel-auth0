<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Auth\User\{Provider, Repository};
use Auth0\Laravel\Contract\ServiceProvider as ServiceProviderContract;
use Auth0\Laravel\Http\Controller\Stateful\{Callback, Login, Logout};
use Auth0\Laravel\Http\Middleware\Stateful\{Authenticate, AuthenticateOptional};
use Auth0\Laravel\Http\Middleware\Stateless\{Authorize, AuthorizeOptional};
use Auth0\Laravel\Http\Middleware\Guard as ShouldUseMiddleware;
use Auth0\Laravel\Store\LaravelSession;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

final class ServiceProvider extends LaravelServiceProvider implements ServiceProviderContract
{
    public function boot(
        Router $router,
        AuthManager $auth,
    ): self {
        $this->mergeConfigFrom(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'config', 'auth0.php']), 'auth0');
        $this->publishes([implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'config', 'auth0.php']) => config_path('auth0.php')], 'auth0-config');

        $auth->extend('auth0.guard', static fn (Application $app, string $name, array $config): Guard => new Guard($name, $config));
        $auth->provider('auth0.provider', static fn (Application $app, array $config): Provider => new Provider($config));

        $router->aliasMiddleware('auth0.authenticate.optional', AuthenticateOptional::class);
        $router->aliasMiddleware('auth0.authenticate', Authenticate::class);
        $router->aliasMiddleware('auth0.authorize.optional', AuthorizeOptional::class);
        $router->aliasMiddleware('auth0.authorize', Authorize::class);
        $router->aliasMiddleware('guard', ShouldUseMiddleware::class);

        return $this;
    }

    public function provides()
    {
        return [
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
            ShouldUseMiddleware::class
        ];
    }

    public function register(): self
    {
        $this->app->singleton(Auth0::class, static fn (): Auth0 => new Auth0());
        $this->app->singleton(Authenticate::class, static fn (): Authenticate => new Authenticate());
        $this->app->singleton(AuthenticateOptional::class, static fn (): AuthenticateOptional => new AuthenticateOptional());
        $this->app->singleton(Authorize::class, static fn (): Authorize => new Authorize());
        $this->app->singleton(AuthorizeOptional::class, static fn (): AuthorizeOptional => new AuthorizeOptional());
        $this->app->singleton(Callback::class, static fn (): Callback => new Callback());
        $this->app->singleton(Login::class, static fn (): Login => new Login());
        $this->app->singleton(Logout::class, static fn (): Logout => new Logout());
        $this->app->singleton(Provider::class, static fn (): Provider => new Provider());
        $this->app->singleton(Repository::class, static fn (): Repository => new Repository());
        $this->app->singleton(ShouldUseMiddleware::class, static fn (): ShouldUseMiddleware => new ShouldUseMiddleware());

        $this->app->singleton('auth0', static fn (): Auth0 => app(Auth0::class));
        $this->app->singleton('auth0.guard', static fn (): Guard => app(Guard::class));
        $this->app->singleton('auth0.provider', static fn (): Provider => app(Provider::class));
        $this->app->singleton('auth0.repository', static fn (): Repository => app(Repository::class));

        return $this;
    }
}
