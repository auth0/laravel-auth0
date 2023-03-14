<?php

declare(strict_types=1);

namespace Auth0\Laravel\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Auth0\Laravel\ServiceProvider;

class TestCase extends BaseTestCase
{
    protected $enablesPackageDiscoveries = true;
    protected $events = [];

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('auth', [
            'defaults' => [
                'guard' => 'testGuard',
                'passwords' => 'users',
            ],
            'guards' => [
                'web' => [
                    'driver' => 'session',
                    'provider' => 'users',
                ],
                'testGuard' => [
                    'driver' => 'auth0.guard',
                    'provider' => 'testProvider',
                ],
            ],
            'providers' => [
                'users' => [
                    'driver' => 'eloquent',
                    'model' => App\Models\User::class,
                ],
                'testProvider' => [
                    'driver' => 'auth0.provider',
                    'model' => 'auth0.repository',
                ],
            ],
        ]);

        // Default to no strategy for testing
        $app['config']->set('auth0.strategy', 'none');

        // Set a random key for testing
        $app['config']->set('app.key', 'base64:' . base64_encode(random_bytes(32)));

        // Setup database for testing (currently unused)
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    /**
     * Asserts that an event was dispatched. Optionally assert the number of times it was dispatched and/or that it was dispatched after another event.
     *
     * @param string $expectedEvent The event to assert was dispatched.
     * @param int $times The number of times the event was expected to be dispatched.
     * @param string|null $followingEvent The event that was expected to be dispatched before this event.
     */
    protected function assertDispatched(string $expectedEvent, int $times = 0, ?string $followingEvent = null)
    {
        $this->assertTrue(\in_array($expectedEvent, $this->events), 'Event ' . $expectedEvent . ' was not dispatched.');

        if ($times > 0) {
            $this->assertTrue(array_count_values($this->events)[$expectedEvent] === $times, 'Event ' . $expectedEvent . ' was not dispatched ' . $times . ' times.');
        }

        if (null !== $followingEvent) {
            $this->assertTrue(\in_array($followingEvent, $this->events));

            $indexExpected = array_search($expectedEvent, $this->events);
            $indexFollowing = array_search($followingEvent, $this->events);

            if ($indexExpected !== false && $indexFollowing !== false) {
                $this->assertTrue($indexExpected > $indexFollowing, 'Event ' . $expectedEvent . ' was not dispatched after ' . $followingEvent . '.');
            }
        }
    }

    /**
     * Asserts that events were dispatched in the order provided. Events not in the array are ignored.
     *
     * @param array<string> $events Array of events to assert were dispatched in order.
     */
    protected function assertDispatchedOrdered(array $events)
    {
        $previousIndex = -1;

        foreach ($events as $event) {
            $index = array_search($event, $this->events);
            $this->assertTrue($index !== false);
            $this->assertTrue($index > $previousIndex);
            $previousIndex = $index;
        }
    }
}
