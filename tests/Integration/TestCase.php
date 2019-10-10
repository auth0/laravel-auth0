<?php

namespace Auth0\Login\Tests\Integration;

use Auth0\Login\LoginServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * @inheritDoc
     *
     * We setup some application config since some of the constructors
     * in the Auth0 SDK validate the configuration.
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('laravel-auth0', [
            'domain' => 'auth0-laravel-testing',
            'client_id' => 'auth0-laravel-testing-client',
            'client_secret' => 'auth0-laravel-testing-secret',
            'redirect_uri' => 'localhost'
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [LoginServiceProvider::class];
    }
}
