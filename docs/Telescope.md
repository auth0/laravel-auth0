# Laravel Telescope

As of 7.11.0, the Auth0 Laravel SDK is compatible with Laravel's Telescope debugging package. However, there are some caveats to be aware of when using the two together.

## Cause of Potential Issues

Issues stem from the fact that Telescope attempts to attribute events it's recording to the authenticated user. While this is useful information to log, it presents a problem. Because Telescope hooks into a number number of events (including the cache, queries, and events system) that the SDK raises during its authentication resolution process, this can cause an infinite loop.

When a request to your application occurs, the SDK works to determine if the end user is authenticated. It executes a number of authenticated related events that Telescope happens to record by default. When these events are recorded by Telescope it asks the authentication API to determine if the end user is authenticated, which in turn calls the SDK to determine if the end user is authenticated, and thus the loop begins.

7.11.0 introduced special checks for when Telescope is installed to prevent this from occurring, but it may not cover all cases.

If you are encountering Telescope causing infinite loops, you may need to disable the offending watchers in your `config/telescope.php` file. Alternatively, you can try wrapping any problematic code in Telescope's `withoutRecording()` method to prevent it from being recorded by Telescope. For example:

```php
\Laravel\Telescope\Telescope::withoutRecording(function () {
    // Your code here...
});
```

## Missing Authentication Information from Telescope

A side effect of the workarounds introduced in 7.11.0 that prevent Telescope from causing infinite loops is that Telescope may be unable to attribute recorded events triggered by the SDK to the authenticated user. This is intentional and necessary, and not a bug.

## SDK <7.11.0 Workarounds

In versions prior to 7.11.0, you may encounter a compatibility issue with the SDK and Telescope when installed and enabled together. You may need to disable offending watchers in your `config/telescope.php` file to resolve this.

For example, if you are encountering issues with Telescope's `EventWatcher`, you can disable it in your `config/telescope.php` file, or ignore specific SDK events that are causing the issue. For example:

```php
<?php

use Laravel\Telescope\Http\Middleware\Authorize;
use Laravel\Telescope\Watchers;

return [
    'watchers' => [
        Watchers\EventWatcher::class => [
            'enabled' => env('TELESCOPE_EVENT_WATCHER', true),
            'ignore' => [
                \Auth0\Laravel\Events\Configuration\BuiltConfigurationEvent::class,
                \Auth0\Laravel\Events\Configuration\BuildingConfigurationEvent::class,
            ],
        ],
    ],

    // Other configuration options left out for brevity...
];
```
