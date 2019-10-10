<?php

namespace Auth0\Login\Tests\Integration\Facade;

use Auth0\Login\Auth0Service;
use Auth0\Login\Tests\Integration\TestCase;

class Auth0Test extends TestCase
{
    public function testFacadeAccessorResolvesToAuth0Service()
    {
        $this->assertInstanceOf(Auth0Service::class, $this->app->make('auth0'));
    }
}
