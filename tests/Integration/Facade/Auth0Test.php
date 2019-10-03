<?php

namespace Auth0\Login\Tests\Integration;

use Auth0\Login\Auth0Service;

class Auth0Test extends Testcase
{
    public function testFacadeAccessorResolvesToAuth0Service()
    {
        $this->assertInstanceOf(Auth0Service::class, $this->app->get('auth0'));
    }
}
