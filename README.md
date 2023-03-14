![laravel-auth0](https://cdn.auth0.com/website/sdks/banners/laravel-auth0-banner.png)

Laravel SDK for [Auth0](https://auth0.com) Authentication and Management APIs.

[![Package](https://img.shields.io/packagist/dt/auth0/login)](https://packagist.org/packages/auth0/laravel-auth0)
[![License](https://img.shields.io/packagist/l/auth0/login)](https://doge.mit-license.org/)

:books: [Documentation](#documentation) - :rocket: [Getting Started](#getting-started) - :speech_balloon: [Feedback](#feedback)

## Documentation

-   Stateful Applications
    -   [Quickstart](https://auth0.com/docs/quickstart/webapp/laravel) — add login, logout and user information to a Laravel application using Auth0.
    -   [Sample Application](https://github.com/auth0-samples/auth0-laravel-php-web-app) — a sample Laravel web application integrated with Auth0.
-   Stateless Applications
    -   [Quickstart](https://auth0.com/docs/quickstart/backend/php) — add access token handling and route authorization to a backend Laravel application using Auth0.
    -   [Sample Application](https://github.com/auth0-samples/auth0-laravel-api-samples) — a sample Laravel backend application integrated with Auth0.
-   [Examples](./EXAMPLES.md) — code samples for common scenarios.
-   [Docs site](https://www.auth0.com/docs) — explore our docs site and learn more about Auth0.

## Getting Started

### Requirements

-   PHP 8.0+
-   [Composer](https://getcomposer.org/)
-   Laravel 9.x/10.x
    -   `Illuminate\Session\Middleware\StartSession` enabled in `app/Http/Kernel.php`
-   PHP Extensions:
    -   [mbstring](https://www.php.net/manual/en/book.mbstring.php)
-   Dependencies:
    -   [PSR-18 HTTP Client implementation](https://github.com/auth0/auth0-PHP/FAQ.md#what-is-psr-18)
    -   [PSR-17 HTTP Factory implementation](https://github.com/auth0/auth0-PHP/FAQ.md#what-is-psr-17)
    -   [PSR-7 HTTP Messages implementation](https://github.com/auth0/auth0-PHP/FAQ.md#what-is-psr-7)

> Please review our [support policy](#support-policy) for details on our PHP and Laravel version support.

> [Octane support](#octane-support) is experimental and not advisable for use in production at this time.

### Installation

Ensure you have [the necessary dependencies](#requirements) installed, then add the SDK to your application using [Composer](https://getcomposer.org/):

```
composer require auth0/login
```

### Configure Auth0

Create a **Regular Web Application** in the [Auth0 Dashboard](https://manage.auth0.com/#/applications). Verify that the "Token Endpoint Authentication Method" is set to `POST`.

Next, configure the callback and logout URLs for your application under the "Application URIs" section of the "Settings" page:

-   **Allowed Callback URLs**: The URL of your application where Auth0 will redirect to during authentication, e.g., `http://localhost:3000/callback`.
-   **Allowed Logout URLs**: The URL of your application where Auth0 will redirect to after user logout, e.g., `http://localhost:3000/login`.

Note the **Domain**, **Client ID**, and **Client Secret**. These values will be used during configuration later.

### Publish SDK configuration

Use Laravel's CLI to generate an Auth0 configuration file within your project:

```
php artisan vendor:publish --tag auth0-config
```

A new file will appear within your project, `app/config/auth0.php`. You should avoid making changes to this file directly.

### Configure your `.env` file

Open the `.env` file within your application's directory, and add the following lines appropriate for your application type:

<details>
    <summary>For Stateful Web Applications</summary>

```
AUTH0_DOMAIN="Your Auth0 domain"
AUTH0_CLIENT_ID="Your Auth0 application client ID"
AUTH0_CLIENT_SECRET="Your Auth0 application client secret"
AUTH0_COOKIE_SECRET="A randomly generated string"
```

Provide a sufficiently long, random string for your `AUTH0_COOKIE_SECRET` using `openssl rand -hex 32`.

</details>

<details>
    <summary>For Stateless Backend Applications</summary>

```
AUTH0_STRATEGY="api"
AUTH0_DOMAIN="Your Auth0 domain"
AUTH0_CLIENT_ID="Your Auth0 application client ID"
AUTH0_CLIENT_SECRET="Your Auth0 application client secret"
AUTH0_AUDIENCE="Your Auth0 API identifier"
```

</details>

### Setup your Laravel application

Integrating the SDK's Guard requires changes to your `config\auth.php` file.

To begin, find the `defaults` section. Set the default `guard` to `auth0`, like this:

```php
// 📂 config/auth.php
'defaults' => [
    'guard' => 'auth0',
    // 📝 Leave any other settings in this section alone.
],
```

Next, find the `guards` section, and add `auth0` there:

```php
// 👆 Continued from above, in config/auth.php
'guards' => [
    // 📝 Any additional guards you use should stay here, too.
    'auth0' => [
        'driver' => 'auth0',
        'provider' => 'auth0',
    ],
],
```

Next, find the `providers` section, and add `auth0` there as well:

```php
// 👆 Continued from above, in config/auth.php
'providers' => [
    // 📝 Any additional providers you use should stay here, too.
    'auth0' => [
        'driver' => 'auth0',
        'repository' => \Auth0\Laravel\Auth\User\Repository::class
    ],
],
```

Although it is enabled by default, now is a good time to ensure the `StartSession` middleware is enabled in your `app/Http/Kernel.php` file:

```php
protected $middlewareGroups = [
    'web' => [
        // ...
        \Illuminate\Session\Middleware\StartSession::class,
        // ...
    ],
];
```

## Add login to stateful web applications

For regular web applications that provide login and logout, we provide prebuilt route controllers to add to your `app/routes/web.php` file that will automatically handle your application's authentication flow with Auth0 for you:

```php
Route::get('/login', \Auth0\Laravel\Http\Controller\Stateful\Login::class)->name('login');
Route::get('/logout', \Auth0\Laravel\Http\Controller\Stateful\Logout::class)->name('logout');
Route::get('/auth0/callback', \Auth0\Laravel\Http\Controller\Stateful\Callback::class)->name('auth0.callback');
```

## Protect routes with middleware

This SDK includes middleware to simplify either authenticating (regular web applications) or authorizing (backend api applications) your Laravel routes, depending on your application type.

<details>
<summary>Stateful Web Applications</summary>

These are for traditional applications that handle logging in and out.

The `auth0.authenticate` middleware will check for an available user session and redirect any requests without one to the login route:

```php
Route::get('/required', function () {
    return view('example.user.template');
})->middleware(['auth0.authenticate']);
```

The `auth0.authenticate.optional` middleware will check for an available user session, but won't reject or redirect requests without one, allowing you to treat such requests as "guest" requests:

```php
Route::get('/', function () {
    if (Auth::check()) {
        return view('example.user.template');
    }

    return view('example.guest.template');
})->middleware(['auth0.authenticate.optional']);
```

> Note that the `example.user.template` and `example.guest.templates` views are just examples and are not part of the SDK; replace these as appropriate for your application.

</details>

<details>
<summary>Stateless Backend Applications</summary>

These are applications that accept an a Access Token through the 'Authorization' header of a request.

The `auth0.authorize` middleware will resolve a Access Token and reject any request with an invalid token.

```php
Route::get('/api/private', function () {
    return response()->json([
        'message' => 'Hello from a private endpoint! You need to be authenticated to see this.',
        'authorized' => Auth::check(),
        'user' => Auth::check() ? json_decode(json_encode((array) Auth::user(), JSON_THROW_ON_ERROR), true) : null,
    ], 200, [], JSON_PRETTY_PRINT);
})->middleware(['auth0.authorize']);
```

The `auth0.authorize` middleware also allows you to optionally filter requests for access tokens based on scopes:

```php
Route::get('/api/private-scoped', function () {
    return response()->json([
        'message' => 'Hello from a private endpoint! You need to be authenticated and have a scope of read:messages to see this.',
        'authorized' => Auth::check(),
        'user' => Auth::check() ? json_decode(json_encode((array) Auth::user(), JSON_THROW_ON_ERROR), true) : null,
    ], 200, [], JSON_PRETTY_PRINT);
})->middleware(['auth0.authorize:read:messages']);
```

The `auth0.authorize.optional` middleware will resolve an available Access Token, but won't block requests without one. This is useful when you want to treat tokenless requests as "guests":

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

Our support lifecycle mirrors the [Laravel release support](https://laravel.com/docs/releases#support-policy) and [PHP release support schedule](https://www.php.net/supported-versions.php).

| SDK Version | Laravel Version | PHP Version | Support Ends |
| ----------- | --------------- | ----------- | ------------ |
| 7.5+        | 10              | 8.2         | Feb 2024     |
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
