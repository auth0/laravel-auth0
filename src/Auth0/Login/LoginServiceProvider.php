<?php namespace Auth0\Login;

use Illuminate\Support\ServiceProvider;
use Auth0\SDK\API\ApiClient;

class LoginServiceProvider extends ServiceProvider {

    const SDK_VERSION = "2.1.1";

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        \Auth::extend('auth0', function($app) {

            // Let the container build the repository for us
            $userRepository = \App::make('\Auth0\Login\Contract\Auth0UserRepository');

            $provider =  new Auth0UserProvider($userRepository);

            return new \Illuminate\Auth\Guard($provider, $app['session.store']);

        });

        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('laravel-auth0.php'),
        ]);

        $laravel = app();
        ApiClient::addHeaderInfoMeta('Laravel:'.$laravel::VERSION);
        ApiClient::addHeaderInfoMeta('SDK:'.self::SDK_VERSION);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Bind the auth0 name to a singleton instance of the Auth0 Service
        $this->app->singleton("auth0", function() {
            return new Auth0Service();
        });

        // When Laravel logs out, logout the auth0 SDK trough the service
        \Event::listen('auth.logout', function() {
            \App::make("auth0")->logout();
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
