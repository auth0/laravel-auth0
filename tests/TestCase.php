<?php

declare(strict_types=1);

namespace Auth0\Laravel\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function refreshServiceProvider(): void
    {
        (new \Auth0\Laravel\ServiceProvider($this->app))->packageBooted();
    }

    protected function getPackageProviders($app)
    {
        return [
            \Auth0\Laravel\ServiceProvider::class,
        ];
    }
}
