<?php

namespace Auth0\Login\Tests\Integration;

use Auth0\Login\LaravelSessionStore;

class LaravelSessionStoreTest extends TestCase
{
    /**
     * @var LaravelSessionStore
     */
    private $sessionStorage;

    public function setUp(): void
    {
        parent::setUp();
        $this->sessionStorage = new LaravelSessionStore();
    }

    public function testAutoPrependsAuth0DoubleUnderscore()
    {
        $testValue = 'Hello world!';

        $this->sessionStorage->set('testkey', $testValue);

        $this->assertEquals($testValue, $this->app->get('session')->get('auth0__testkey'));
    }

    /**
     * Because the LaravelSessionHandler uses the Session Facade, we can easily replace
     * our storage driver without changing any code.
     */
    public function testDifferentSessionStorage()
    {
        $this->app['config']->set('session.driver', 'database');
        $this->app['config']->set('session.table', 'sessions');
        $this->app['config']->set('database.default', 'sessions');
        $this->app['config']->set('database.connections.sessions', ['driver' => 'sqlite', 'database' => ':memory:']);
        $testValue = 'Its happening!';
        $this->artisan('migrate');

        $this->sessionStorage->set('testkey', $testValue);
        $this->app->get('session')->save();

        $sessionsInDb = $this->app->get('db')->select('select * from sessions');
        $this->assertEquals(1, count($sessionsInDb));
    }
}
