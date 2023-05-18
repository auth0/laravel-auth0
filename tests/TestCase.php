<?php

declare(strict_types=1);

namespace Auth0\Laravel\Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Auth0\Laravel\ServiceProvider;
use Orchestra\Testbench\Concerns\CreatesApplication;
use Spatie\LaravelRay\RayServiceProvider;

class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $enablesPackageDiscoveries = true;
    protected $events = [];

    protected function getPackageProviders($app)
    {
        return [
            RayServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('auth', [
            'defaults' => [
                'guard' => 'legacyGuard',
                'passwords' => 'users',
            ],
            'guards' => [
                'web' => [
                    'driver' => 'session',
                    'provider' => 'users',
                ],
                'legacyGuard' => [
                    'driver' => 'auth0.guard',
                    'configuration' => 'web',
                    'provider' => 'auth0-provider',
                ],
                'auth0-session' => [
                    'driver' => 'auth0.authenticator',
                    'configuration' => 'web',
                    'provider' => 'auth0-provider',
                ],
                'auth0-api' => [
                    'driver' => 'auth0.authorizer',
                    'configuration' => 'api',
                    'provider' => 'auth0-provider',
                ],
            ],
            'providers' => [
                'users' => [
                    'driver' => 'eloquent',
                    'model' => App\Models\User::class,
                ],
                'auth0-provider' => [
                    'driver' => 'auth0.provider',
                    'repository' => 'auth0.repository',
                ],
            ],
        ]);

        // Set a random key for testing
        $_ENV['APP_KEY'] = 'base64:' . base64_encode(random_bytes(32));

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
        expect($this->events)
            ->toBeArray()
            ->toContain($expectedEvent);

        if ($times > 0) {
            expect(array_count_values($this->events)[$expectedEvent])
                ->toBeInt()
                ->toBe($times);
        }

        if (null !== $followingEvent) {
            expect($this->events)
                ->toContain($followingEvent);

            $indexExpected = array_search($expectedEvent, $this->events);
            $indexFollowing = array_search($followingEvent, $this->events);

            if ($indexExpected !== false && $indexFollowing !== false) {
                expect($indexExpected)
                    ->toBeInt()
                    ->toBeGreaterThan($indexFollowing);
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

            expect($index)
                ->toBeInt()
                ->toBeGreaterThan($previousIndex);

            $previousIndex = $index;
        }
    }
}
