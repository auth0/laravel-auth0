<?php

namespace Auth0\Login;

use Auth0\SDK\API\Helpers\ApiClient;
use Auth0\SDK\API\Helpers\InformationHeaders;
use Auth0\SDK\API\Helpers\State\StateHandler;
use Auth0\SDK\API\Helpers\State\SessionStateHandler;
use Auth0\SDK\Store\StoreInterface;
use Illuminate\Support\ServiceProvider;

class LoginServiceProvider extends ServiceProvider
{

    const SDK_VERSION = "5.4.0";

    /**
     * Bootstrap the application events.
     */
    public function boot()
    {
        \Auth::provider('auth0', function ($app, array $config) {
            return $app->make(Auth0UserProvider::class);
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

        $this->app->bind(StateHandler::class, function ($app) {
            return new SessionStateHandler($app->make(LaravelSessionStore::class));
        });

        // Bind the auth0 name to a singleton instance of the Auth0 Service
        $this->app->singleton(Auth0Service::class, function ($app) {
            return new Auth0Service(
                $app->make('config')->get('laravel-auth0'),
                $app->make(StoreInterface::class),
                $app->make(StateHandler::class)
            );
        });
        $this->app->singleton('auth0', function () {
            return $this->app->make(Auth0Service::class);
        });

        // When Laravel logs out, logout the auth0 SDK trough the service
        \Event::listen('auth.logout', function () {
            \App::make('auth0')->logout();
        });
        \Event::listen('user.logout', function () {
            \App::make('auth0')->logout();
        });
        \Event::listen('Illuminate\Auth\Events\Logout', function () {
            \App::make('auth0')->logout();
        });
    }
}
