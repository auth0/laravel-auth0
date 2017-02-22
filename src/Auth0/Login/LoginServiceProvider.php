<?php

namespace Auth0\Login;

use Illuminate\Support\ServiceProvider;
use Auth0\SDK\API\Helpers\ApiClient;
use Auth0\SDK\API\Helpers\InformationHeaders;

class LoginServiceProvider extends ServiceProvider {

    const SDK_VERSION = "4.0.4";

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

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

            $infoHeaders->setEnvironment('Laravel', $laravel::VERSION);
            $infoHeaders->setPackage('laravel-auth0', self::SDK_VERSION);

            ApiClient::setInfoHeadersData($infoHeaders);
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        // Bind the auth0 name to a singleton instance of the Auth0 Service
        $this->app->singleton('auth0', function () {
          return new Auth0Service();
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

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }
}
