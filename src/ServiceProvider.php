<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Illuminate\Contracts\Http\Kernel;
use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Auth\Guards\{SessionGuard, TokenGuard};
use Auth0\Laravel\Auth\User\{Provider, Repository};
use Auth0\Laravel\Contract\Auth\GuardContract;
use Auth0\Laravel\Contract\ServiceProviderContract;
use Auth0\Laravel\Http\Controller\Stateful\{Callback, Login, Logout};
use Auth0\Laravel\Http\Middleware\Authenticator;
use Auth0\Laravel\Http\Middleware\Authorizer;
use Auth0\Laravel\Http\Middleware\Guard as GuardMiddleware;
use Auth0\Laravel\Http\Middleware\Stateful\{Authenticate, AuthenticateOptional};
use Auth0\Laravel\Http\Middleware\Stateless\{Authorize, AuthorizeOptional};
use Auth0\Laravel\Store\LaravelSession;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Support\Facades\Route;

final class ServiceProvider extends LaravelServiceProvider implements ServiceProviderContract
{
    public function boot(
        Router $router,
        AuthManager $auth,
        Gate $gate,
    ): self {
        $this->mergeConfigFrom(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'config', 'auth0.php']), 'auth0');
        $this->publishes([implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'config', 'auth0.php']) => config_path('auth0.php')], 'auth0');

        $auth->extend('auth0.guard', static fn (Application $app, string $name, array $config): Guard => new Guard($name, $config));
        $auth->extend('auth0.authenticator', static fn (Application $app, string $name, array $config): SessionGuard => new SessionGuard($name, $config));
        $auth->extend('auth0.authorizer', static fn (Application $app, string $name, array $config): TokenGuard => new TokenGuard($name, $config));

        $auth->provider('auth0.provider', static fn (Application $app, array $config): Provider => new Provider($config));

        $router->aliasMiddleware('auth0.authenticate.optional', AuthenticateOptional::class);
        $router->aliasMiddleware('auth0.authenticate', Authenticate::class);
        $router->aliasMiddleware('auth0.authorize.optional', AuthorizeOptional::class);
        $router->aliasMiddleware('auth0.authorize', Authorize::class);
        $router->aliasMiddleware('guard', GuardMiddleware::class);

        $gate->define('scope', static function (Authenticatable $user, string $scope, ?GuardContract $guard = null) : bool {
            $guard ??= auth()->guard();
            if (! $guard instanceof GuardContract) {
                return false;
            }
            return $guard->hasScope($scope);
        });

        $gate->define('permission', static function (Authenticatable $user, string $permission, ?GuardContract $guard = null) : bool {
            $guard ??= auth()->guard();
            if (! $guard instanceof GuardContract) {
                return false;
            }
            return $guard->hasPermission($permission);
        });

        $gate->before(static function ($user, $ability) {
            $guard = auth()->guard();
            if (! $guard instanceof GuardContract || ! $user instanceof Authenticatable || ! is_string($ability)) {
                return;
            }
            if (str_starts_with($ability, 'scope:')) {
                if ($guard->hasScope(substr($ability, 6))) {
                    return Response::allow();
                }

                return Response::deny();
            }
            if (str_contains($ability, ':')) {
                if ($guard->hasPermission($ability)) {
                    return Response::allow();
                }

                return Response::deny();
            }
        });

        if (config('auth0.registerMiddleware') === true) {
            $kernel = app()->make(Kernel::class);
            $kernel->prependMiddlewareToGroup('web', Authenticator::class);
            $kernel->prependMiddlewareToGroup('api', Authorizer::class);
        }

        if (config('auth0.registerAuthenticationRoutes') === true) {
            Route::group(['middleware' => 'web'], static function () : void {
                Route::get('/login', Login::class)->name('login');
                Route::get('/logout', Logout::class)->name('logout');
                Route::get('/callback', Callback::class)->name('callback');
            });
        }

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
            SessionGuard::class,
            GuardMiddleware::class,
            TokenGuard::class,
        ];
    }

    public function register(): self
    {
        if (config('auth0.registerGuards') === true) {
            if (null === config('auth.guards.auth0-session')) {
                config([
                    'auth.guards.auth0-session' => [
                        'driver' => 'auth0.authenticator',
                        'configuration' => 'web',
                        'provider' => 'auth0-provider',
                    ],
                ]);
            }

            if (null === config('auth.guards.auth0-api')) {
                config([
                    'auth.guards.auth0-api' => [
                        'driver' => 'auth0.authorizer',
                        'configuration' => 'api',
                        'provider' => 'auth0-provider',
                    ],
                ]);
            }

            if (null === config('auth.providers.auth0-provider')) {
                config([
                    'auth.providers.auth0-provider' => [
                        'driver' => 'auth0.provider',
                        'repository' => \Auth0\Laravel\Auth\User\Repository::class,
                    ],
                ]);
            }
        }

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
        $this->app->singleton(GuardMiddleware::class, static fn (): GuardMiddleware => new GuardMiddleware());

        $this->app->singleton('auth0', static fn (): Auth0 => app(Auth0::class));
        $this->app->singleton('auth0.repository', static fn (): Repository => app(Repository::class));

        return $this;
    }
}
