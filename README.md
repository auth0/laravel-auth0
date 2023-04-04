![laravel-auth0](https://cdn.auth0.com/website/sdks/banners/laravel-auth0-banner.png)

Laravel SDK for [Auth0](https://auth0.com) Authentication and Management APIs.

[![Package](https://img.shields.io/packagist/dt/auth0/login)](https://packagist.org/packages/auth0/laravel-auth0)
![Build Status](https://img.shields.io/github/checks-status/auth0/laravel-auth0/main)
[![Coverage](https://img.shields.io/codecov/c/github/auth0/laravel-auth0/main)](https://codecov.io/gh/auth0/laravel-auth0)
[![License](https://img.shields.io/packagist/l/auth0/login)](https://doge.mit-license.org/)

:books: [Documentation](#documentation) - :rocket: [Getting Started](#getting-started) - :speech_balloon: [Feedback](#feedback)

## Documentation

-   Stateful Applications
    -   [Quickstart](https://auth0.com/docs/quickstart/webapp/laravel) — add login, logout, and user information to a Laravel application using Auth0.
    -   [Sample Application](https://github.com/auth0-samples/auth0-laravel-php-web-app) — a sample Laravel web application integrated with Auth0.
-   Stateless Applications
    -   [Quickstart](https://auth0.com/docs/quickstart/backend/php) — add access token handling and route authorization to a backend Laravel application using Auth0.
    -   [Sample Application](https://github.com/auth0-samples/auth0-laravel-api-samples) — a sample Laravel backend application integrated with Auth0.
-   [Examples](./EXAMPLES.md) — code samples for common scenarios.
-   [Docs site](https://www.auth0.com/docs) — explore our docs site and learn more about Auth0.

## Getting Started

### Requirements

-   Laravel 10 (PHP 8.1+) or Laravel 9 (PHP 8.0+)
-   [Composer](https://getcomposer.org/)
-   [Auth0 account](https://auth0.com/signup)

> Please review our [support policy](#support-policy) for details on our PHP and Laravel version support.

### Installation

Open a shell to the root of your Laravel application directory, and import the SDK using [Composer](https://getcomposer.org/):

```bash
composer require auth0/login
```

Next, generate the `config/auth0.php` configuration file for your application:

```bash
php artisan vendor:publish --tag=auth0-config
```

### Create an Auth0 Application

First, [install the Auth0 CLI](https://github.com/auth0/auth0-cli#installation) and [authenticate to your tenant](https://github.com/auth0/auth0-cli#authenticating-to-your-tenant).

Next, create a new Auth0 application using the CLI:

```bash
auth0 apps create \
  --name "My Auth0 Laravel App" \
  --type "regular" \
  --auth-method "post" \
  --callbacks "http://localhost:8000/callback" \
  --logout-urls "http://localhost:8000/login" \
  --reveal-secrets \
  --no-input
```

Make note of your tenant's **Domain** (e.g. `tenant.region.auth0.com`), **Client ID**, and **Client Secret** returned. These will be required later during configuration.

### Determine Your Application Type

This SDK supports two application types: **stateful** and **stateless**.

-   **Stateful** applications use a session to store user information (their state).
    -   These provide a login/logout experience.
    -   These are **authenticating** users.
    -   These often need to know the **identity** of the requestor.
    -   These are often considered traditional web applications.
-   **Stateless** applications authorize requests to routes.
    -   These use Access Tokens to firewall requests.
    -   These are **authorizing** requests.
    -   These are agnostic to the identity of the requestor.
    -   These are typically considered backend services.
    -   These are often used to provide data to single-page applications.

It's important to understand the differences between these two application types, and which one is appropriate for your application.

> **Note**
> The SDK does not support simultaneously using stateless and stateful guards within the same application at this time. If you need to support both, you will need to create two separate application instances. Support for this is planned for a future release.

### Configure the SDK

Open the `.env` file within your application's directory, and add the following lines appropriate for your application type:

<details>
    <summary>Stateful Applications</summary>

```
AUTH0_DOMAIN="Your Auth0 domain"
AUTH0_CLIENT_ID="Your Auth0 application client ID"
AUTH0_CLIENT_SECRET="Your Auth0 application client secret"
AUTH0_COOKIE_SECRET="A randomly generated string"
```

Provide a sufficiently long, random string for your `AUTH0_COOKIE_SECRET` using `openssl rand -hex 32`.

</details>

<details>
    <summary>Stateless Services</summary>

```
AUTH0_STRATEGY="api"
AUTH0_DOMAIN="Your Auth0 domain"
AUTH0_CLIENT_ID="Your Auth0 application client ID"
AUTH0_CLIENT_SECRET="Your Auth0 application client secret"
AUTH0_AUDIENCE="Your Auth0 API identifier"
```

</details>

### Setup Your Application

Open your `app/config/auth.php` file.

Find the `guards` section, and add a new guard to the `guards` array that uses the `auth0.guard` driver.

```php
'guards' => [
    'someGuardName' => [
        'driver' => 'auth0.guard',
        'provider' => 'someProviderName',
    ],
],
```

Find the `providers` section, and add a new provider to the `providers` array that uses `auth0.provider` as the driver.

```php
    'someProviderName' => [
        'driver' => 'auth0.provider',
        'repository' => \Auth0\Laravel\Auth\User\Repository::class
    ],
],
```

`someGuardName` and `someProviderName` can be any names you choose, but please ensure the `provider` name matches the `provider` value in the guard definition.

## Authentication

**For stateful applications** that use a login/logout experience, the SDK provides a series of routing controllers to handle the authentication flow.

Add these routes where most appropriate for your configuration; `app/routes/web.php` is a common location for most applications.

```php
use Auth0\Laravel\Http\Controller\Stateful\{Login, Logout, Callback};

Route::get('/login', Login::class)->name('login');
Route::get('/logout', Logout::class)->name('logout');
Route::get('/callback', Callback::class)->name('callback');
```

Please ensure requests for these routes are managed by an Auth0 guard configured by your application.

## Routing Protection

The SDK provides a series of routing middleware to help you secure your application's routes. Any routes you wish to protect should be wrapped in the appropriate middleware.

<details>
<summary>Stateful Applications</summary>

**`auth0.authenticate` requires a user to be logged in to access a route.** Other requests will be redirected to the `login` route.

```php
Route::get('/required', function () {
    return view(/* Authenticated */);
})->middleware(['auth0.authenticate']);
```

**`auth0.authenticate.optional` allows anyone to access a route.** It will check if a user is logged in, and if so, will make sure `Auth::user()` is available to the route. This is useful when you wish to display different content to logged-in users and guests.

```php
Route::get('/', function () {
    if (Auth::check()) {
        return view(/* Authenticated */)
    }

    return view(/* Guest */)
})->middleware(['auth0.authenticate.optional']);
```

</details>

<details>
<summary>Stateless Services</summary>

**`auth0.authorize` requires a valid access token for a request.** Otherwise, it will return a `401 Unauthorized` response.

```php
Route::get('/api/private', function () {
    return response()->json([
        'message' => 'Hello from a private endpoint! You need to be authenticated to see this.',
        'authorized' => Auth::check(),
        'user' => Auth::check() ? json_decode(json_encode((array) Auth::user(), JSON_THROW_ON_ERROR), true) : null,
    ], 200, [], JSON_PRETTY_PRINT);
})->middleware(['auth0.authorize']);
```

**`auth0.authorize` can further require access tokens to have a specific scope.** If the scope is not present for the token, it will return a `403 Forbidden` response.

```php
Route::get('/api/private-scoped', function () {
    return response()->json([
        'message' => 'Hello from a private endpoint! You need to be authenticated and have a scope of read:messages to see this.',
        'authorized' => Auth::check(),
        'user' => Auth::check() ? json_decode(json_encode((array) Auth::user(), JSON_THROW_ON_ERROR), true) : null,
    ], 200, [], JSON_PRETTY_PRINT);
})->middleware(['auth0.authorize:read:messages']);
```

**`auth0.authorize.optional` allows anyone to access a route.** It will check if a valid access token is present, and if so, will make sure `Auth::user()` is available to the route. This is useful when you wish to return different responses to authenticated and unauthenticated requests.

```php
Route::get('/api/public', function () {
    return response()->json([
        'message' => 'Hello from a public endpoint! You don\'t need to be authenticated to see this.',
        'authorized' => Auth::check(),
        'user' => Auth::check() ? json_decode(json_encode((array) Auth::user(), JSON_THROW_ON_ERROR), true) : null,
    ], 200, [], JSON_PRETTY_PRINT);
})->middleware(['auth0.authorize.optional']);
```

</details>

## Support Policy

Our support lifecycle mirrors the [Laravel release support](https://laravel.com/docs/releases#support-policy) and [PHP release support](https://www.php.net/supported-versions.php) schedules.

| SDK Version | Laravel Version | PHP Version | Support Ends |
| ----------- | --------------- | ----------- | ------------ |
| 7.5+        | 10              | 8.2         | Feb 2025     |
|             |                 | 8.1         | Nov 2024     |
| 7.0+        | 9               | 8.2         | Feb 2024     |
|             |                 | 8.1         | Feb 2024     |
|             |                 | 8.0         | Nov 2023     |

We drop support for Laravel and PHP versions when they reach end-of-life and cease receiving security fixes from Laravel and the PHP Foundation, whichever comes first. Please ensure your environment remains up to date so you can continue receiving updates for Laravel, PHP, and this SDK.

## Octane Support

Octane compatibility is currently considered experimental and unsupported.

Although we are working toward ensuring the SDK is fully compatible with this feature, we do not recommend using this with our SDK in production until we have full confidence and announced support. Due to the aggressive changes Octane makes to Laravel's core behavior, there is an opportunity for problems we haven't fully identified or resolved yet.

Feedback and bug fix contributions are greatly appreciated as we work toward full. Octane support.

## Feedback

### Contributing

We appreciate feedback and contribution to this repo! Before you get started, please see the following:

-   [Auth0's general contribution guidelines](https://github.com/auth0/open-source-template/blob/master/GENERAL-CONTRIBUTING.md)
-   [Auth0's code of conduct guidelines](https://github.com/auth0/open-source-template/blob/master/CODE-OF-CONDUCT.md)

### Raise an issue

To provide feedback or report a bug, [please raise an issue on our issue tracker](https://github.com/auth0/laravel-auth0/issues).

### Vulnerability Reporting

Please do not report security vulnerabilities on the public GitHub issue tracker. The [Responsible Disclosure Program](https://auth0.com/whitehat) details the procedure for disclosing security issues.

---

<p align="center">
  <picture>
    <source media="(prefers-color-scheme: light)" srcset="https://cdn.auth0.com/website/sdks/logos/auth0_light_mode.png" width="150">
    <source media="(prefers-color-scheme: dark)" srcset="https://cdn.auth0.com/website/sdks/logos/auth0_dark_mode.png" width="150">
    <img alt="Auth0 Logo" src="https://cdn.auth0.com/website/sdks/logos/auth0_light_mode.png" width="150">
  </picture>
</p>

<p align="center">Auth0 is an easy-to-implement, adaptable authentication and authorization platform.<br />To learn more, check out <a href="https://auth0.com/why-auth0">"Why Auth0?"</a></p>

<p align="center">This project is licensed under the MIT license. See the <a href="./LICENSE"> LICENSE</a> file for more info.</p>
