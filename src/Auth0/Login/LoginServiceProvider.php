<?php

namespace Auth0\Login;

use Auth0\Login\Contract\Auth0UserRepository as Auth0UserRepositoryContract;
use Auth0\Login\Repository\Auth0UserRepository;
use Auth0\SDK\API\Helpers\ApiClient;
use Auth0\SDK\API\Helpers\InformationHeaders;
use Auth0\SDK\Store\StoreInterface;
use Illuminate\Auth\RequestGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class LoginServiceProvider extends ServiceProvider
{

    const SDK_VERSION = '6.4.0';

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        Auth::provider('auth0', function ($app, array $config) {
            return $app->make(Auth0UserProvider::class);
        });

        Auth::extend('auth0', function ($app, $name, $config) {
            return new RequestGuard(function (Request $request, Auth0UserProvider $provider) {
                return $provider->retrieveByCredentials(['api_token' => $request->bearerToken()]);
            }, $app['request'], $app['auth']->createUserProvider($config['provider']));
        });

        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('laravel-auth0.php'),
        ]);

        $laravel = app();

        $oldInfoHeaders = ApiClient::getInfoHeadersData();

        if ($oldInfoHeaders) {
            $infoHeaders = InformationHeaders::Extend($oldInfoHeaders);

            $infoHeaders->setEnvProperty('Laravel', $laravel::VERSION);
            $infoHeaders->setPackage('laravel-auth0', self::SDK_VERSION);

            ApiClient::setInfoHeadersData($infoHeaders);
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->app->bind(StoreInterface::class, function () {
            return new LaravelSessionStore();
        });

        $this->app->bind(Auth0UserRepositoryContract::class, Auth0UserRepository::class);

        // Bind the auth0 name to a singleton instance of the Auth0 Service
        $this->app->singleton(Auth0Service::class, function ($app) {
            return new Auth0Service(
                $app->make('config')->get('laravel-auth0'),
                $app->make(StoreInterface::class),
                $app->make('cache.store')
            );
        });
        $this->app->singleton('auth0', function () {
            return $this->app->make(Auth0Service::class);
        });

        // When Laravel logs out, logout the auth0 SDK trough the service
        Event::listen('auth.logout', function () {
            app('auth0')->logout();
        });
        Event::listen('user.logout', function () {
            app('auth0')->logout();
        });
        Event::listen('Illuminate\Auth\Events\Logout', function () {
            app('auth0')->logout();
        });
    }
}
