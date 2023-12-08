<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Bridges\{CacheBridge, CacheItemBridge, SessionBridge};
use Auth0\Laravel\Controllers\{CallbackController, LoginController, LogoutController};
use Auth0\Laravel\Guards\{AuthenticationGuard, AuthorizationGuard, GuardContract};
use Auth0\Laravel\Middleware\{AuthenticateMiddleware, AuthenticateOptionalMiddleware, AuthenticatorMiddleware, AuthorizeMiddleware, AuthorizeOptionalMiddleware, AuthorizerMiddleware, GuardMiddleware};
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

use function is_string;

/**
 * @api
 */
abstract class ServiceProviderAbstract extends ServiceProvider
{
    final public function boot(
        Router $router,
        AuthManager $auth,
        Gate $gate,
    ): self {
        $this->mergeConfigFrom(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'config', 'auth0.php']), 'auth0');
        $this->publishes([implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'config', 'auth0.php']) => config_path('auth0.php')], 'auth0');

        $auth->extend('auth0.authenticator', static fn (Application $app, string $name, array $config): AuthenticationGuard => new AuthenticationGuard($name, $config));
        $auth->extend('auth0.authorizer', static fn (Application $app, string $name, array $config): AuthorizationGuard => new AuthorizationGuard($name, $config));
        $auth->provider('auth0.provider', static fn (Application $app, array $config): UserProvider => new UserProvider($config));

        $router->aliasMiddleware('guard', GuardMiddleware::class);

        $gate->define('scope', static function (Authenticatable $user, string $scope, ?GuardContract $guard = null): bool {
            $guard ??= auth()->guard();

            if (! $guard instanceof GuardContract) {
                return false;
            }

            return $guard->hasScope($scope);
        });

        $gate->define('permission', static function (Authenticatable $user, string $permission, ?GuardContract $guard = null): bool {
            $guard ??= auth()->guard();

            if (! $guard instanceof GuardContract) {
                return false;
            }

            return $guard->hasPermission($permission);
        });

        $gate->before(static function (?Authenticatable $user, ?string $ability) {
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

        $this->registerDeprecated($router, $auth);
        $this->registerMiddleware($router);
        $this->registerRoutes();

        return $this;
    }

    final public function provides()
    {
        return [
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
        ];
    }

    final public function register(): self
    {
        $this->registerGuards();

        $this->app->singleton(Auth0::class, static fn (): Service => new Service());
        $this->app->singleton(Service::class, static fn (): Service => new Service());
        $this->app->singleton(Configuration::class, static fn (): Configuration => new Configuration());
        $this->app->singleton(Service::class, static fn (): Service => new Service());
        $this->app->singleton(AuthenticatorMiddleware::class, static fn (): AuthenticatorMiddleware => new AuthenticatorMiddleware());
        $this->app->singleton(AuthorizerMiddleware::class, static fn (): AuthorizerMiddleware => new AuthorizerMiddleware());
        $this->app->singleton(AuthenticateMiddleware::class, static fn (): AuthenticateMiddleware => new AuthenticateMiddleware());
        $this->app->singleton(AuthenticateOptionalMiddleware::class, static fn (): AuthenticateOptionalMiddleware => new AuthenticateOptionalMiddleware());
        $this->app->singleton(AuthorizeMiddleware::class, static fn (): AuthorizeMiddleware => new AuthorizeMiddleware());
        $this->app->singleton(AuthorizeOptionalMiddleware::class, static fn (): AuthorizeOptionalMiddleware => new AuthorizeOptionalMiddleware());
        $this->app->singleton(GuardMiddleware::class, static fn (): GuardMiddleware => new GuardMiddleware());
        $this->app->singleton(CallbackController::class, static fn (): CallbackController => new CallbackController());
        $this->app->singleton(LoginController::class, static fn (): LoginController => new LoginController());
        $this->app->singleton(LogoutController::class, static fn (): LogoutController => new LogoutController());
        $this->app->singleton(UserProvider::class, static fn (): UserProvider => new UserProvider());
        $this->app->singleton(UserRepository::class, static fn (): UserRepository => new UserRepository());

        $this->app->singleton('auth0', static fn (): Service => app(Service::class));
        $this->app->singleton('auth0.repository', static fn (): UserRepository => app(UserRepository::class));

        return $this;
    }

    final public function registerDeprecated(
        Router $router,
        AuthManager $auth,
    ): void {
        $auth->extend('auth0.guard', static fn (Application $app, string $name, array $config): Guard => new Guard($name, $config));

        $router->aliasMiddleware('auth0.authenticate.optional', AuthenticateOptionalMiddleware::class);
        $router->aliasMiddleware('auth0.authenticate', AuthenticateMiddleware::class);
        $router->aliasMiddleware('auth0.authorize.optional', AuthorizeOptionalMiddleware::class);
        $router->aliasMiddleware('auth0.authorize', AuthorizeMiddleware::class);
    }

    /**
     * @codeCoverageIgnore
     */
    final public function registerGuards(): void
    {
        if (true === config('auth0.registerGuards')) {
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
                        'repository' => 'auth0.repository',
                    ],
                ]);
            }
        }
    }

    /**
     * @codeCoverageIgnore
     *
     * @param Router $router
     */
    final public function registerMiddleware(
        Router $router,
    ): void {
        if (true === config('auth0.registerMiddleware')) {
            $kernel = $this->app->make(Kernel::class);

            /**
             * @var \Illuminate\Foundation\Http\Kernel $kernel
             */
            $kernel->appendMiddlewareToGroup('web', AuthenticatorMiddleware::class);
            $kernel->prependToMiddlewarePriority(AuthenticatorMiddleware::class);

            $kernel->appendMiddlewareToGroup('api', AuthorizerMiddleware::class);
            $kernel->prependToMiddlewarePriority(AuthorizerMiddleware::class);
        }
    }

    final public function registerRoutes(): void
    {
        if (true === config('auth0.registerAuthenticationRoutes')) {
            Route::group(['middleware' => 'web'], static function (): void {
                Route::get(Configuration::string(Configuration::CONFIG_NAMESPACE_ROUTES . Configuration::CONFIG_ROUTE_LOGIN) ?? '/login', LoginController::class)->name('login');
                Route::get(Configuration::string(Configuration::CONFIG_NAMESPACE_ROUTES . Configuration::CONFIG_ROUTE_LOGOUT) ?? '/logout', LogoutController::class)->name('logout');
                Route::get(Configuration::string(Configuration::CONFIG_NAMESPACE_ROUTES . Configuration::CONFIG_ROUTE_CALLBACK) ?? '/callback', CallbackController::class)->name('callback');
            });
        }
    }
}
