<?php

namespace Auth0\Login\Tests\Integration;

use Auth0\Login\LaravelSessionStore;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
        $testValue = 'Its happening!';
        $this->app['config']->set('session.driver', 'database');
        $this->app['config']->set('session.connection', 'sessions');
        $this->app['config']->set('database.default', 'sessions');
        $this->app['config']->set('database.connections.sessions', ['driver' => 'sqlite', 'database' => ':memory:']);
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->unique();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('payload');
            $table->integer('last_activity');
        });

        $this->sessionStorage->set('testkey', $testValue);
        $this->app->get('session')->save();

        $sessionsInDb = $this->app->get('db')->select('select * from sessions');
        $this->assertCount(1, $sessionsInDb);
    }
}
