# Examples Cookbook

This document provides example solutions for common integration questions.

-   [Users](#users)
    -   [Custom Models and Repositories](#custom-user-models-and-repositories)
-   [Management API](#management-api)
-   [Middleware](#middleware)
     -   [Scope Filtering](#scope-filtering)
-   [Events](#events)
-   [Testing](#testing)
    -   [Impersonation](#impersonation)

## Users

### Custom User Models and Repositories

[docs/User Models and Repositories](./docs/User%20Models%20and%20Repositories.md) covers extending the SDK to support database storage, [Eloquent](https://laravel.com/docs/10.x/eloquent), and other scenarios.

## Management API

[docs/Management API](./docs/Management%20API.md) covers making API calls to [Auth0 Management API endpoints](https://auth0.com/docs/api/management/v2).

## Middleware

### Scope Filtering

Let's assume you have a route like the following, that is protected by the scope `read:messages`:

```php
Route::get('/api/private-scoped', function () {
    return response()->json([
        'message' => 'Hello from a private endpoint!',
        'authorized' => Auth::check(),
        'user' => Auth::check() ? json_decode(json_encode((array) Auth::user(), JSON_THROW_ON_ERROR), true) : null,
    ], 200, [], JSON_PRETTY_PRINT);
})->middleware(['auth0.authorize:read:messages']);
```

## Events

[docs/Events](./docs/Events.md) covers hooking into [events](https://laravel.com/docs/10.x/events) raised by the SDK to customize behavior.

## Testing

### Impersonation

When writing unit tests for your application that include HTTP requests to routes protected by the SDK's middleware, you can use the "Imposter" trait to simplify fulfilling the request by mocking a user session. The following example is writen in [PEST syntax](https://pestphp.com), but the trait can be used in an identical manner with test-case classes in PHPUnit:

> **Note**
> If you're using custom user repositories or models, you may need to adjust how `$imposter` is shaping the `user` property to match your integration.

```php
<?php

declare(strict_types=1);

use Auth0\Laravel\Entities\Credential;
use Auth0\Laravel\Model\Stateless\User;
use Auth0\Laravel\Traits\Impersonate;
use Illuminate\Support\Facades\Route;

uses(Impersonate::class);

it('impersonates a user for the request', function (): void {
    Route::middleware('auth0.authorize')->get('/example', function () use ($route): string {
        return response()->json('Hello World');
    });
    
    $imposter = Credential::create(
        user: new User(['sub' => uniqid()]),
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->impersonate($imposter)
         ->getJson('./example')
         ->assertStatus(Response::HTTP_OK);
});
```
