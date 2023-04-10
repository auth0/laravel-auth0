![laravel-auth0](https://cdn.auth0.com/website/sdks/banners/laravel-auth0-banner.png)

Laravel SDK for [Auth0](https://auth0.com) Authentication and Management APIs.

[![Package](https://img.shields.io/packagist/dt/auth0/login)](https://packagist.org/packages/auth0/login)
![Build Status](https://img.shields.io/github/checks-status/auth0/laravel-auth0/main)
[![Coverage](https://img.shields.io/codecov/c/github/auth0/laravel-auth0/main)](https://codecov.io/gh/auth0/laravel-auth0)
[![License](https://img.shields.io/packagist/l/auth0/login)](https://doge.mit-license.org/)

:books: [Documentation](#documentation) - :rocket: [Getting Started](#getting-started) - :speech_balloon: [Feedback](#feedback)

## Documentation

-   Quickstart Demonstrations
    -   [Application using Sessions (Stateful)](https://auth0.com/docs/quickstart/laravel/php) — Traditional web application that uses sessions and supports logging in, logging out, and querying user profiles. [The complete source code is also available.](https://github.com/auth0-samples/auth0-laravel-php-web-app)
    -   [API using Access Tokens (Stateless)](https://auth0.com/docs/quickstart/backend/laravel) — Backend service that authorizes endpoints using access tokens provided by a frontend client and returns JSON responses. [The complete source code is also available.](https://github.com/auth0-samples/auth0-laravel-api-samples)
-   [Laravel Examples](./EXAMPLES.md) — Code samples for common scenarios.
-   [Documentation Hub](https://www.auth0.com/docs) — Learn more about integrating Auth0.

## Getting Started

### Requirements

-   Laravel 10 (PHP 8.1+) or Laravel 9 (PHP 8.0+)
-   [Composer](https://getcomposer.org/)
-   [Auth0 account](https://auth0.com/signup)

Our examples use the [Auth0 CLI](https://github.com/auth0/auth0-cli) to help get you kickstarted quickly. You should have the CLI [installed](https://github.com/auth0/auth0-cli#installation) and [authenticated](https://github.com/auth0/auth0-cli#authenticating-to-your-tenant).

> Please review our [support policy](#support-policy) for details on our PHP and Laravel version support.

### Installation

Open a shell to the root of your Laravel application's root directory, and import the SDK using [Composer](https://getcomposer.org/):

```bash
composer require auth0/login
```

Next, generate the `config/auth0.php` configuration file for your application:

```bash
php artisan vendor:publish --tag=auth0-config
```

### Determine Your Application Type

Before we begin configuring your application, it's important to understand the difference between "stateful" and "stateless" applications, and which one is appropriate for your use case.

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

As we continue, we'll use these terms to help guide you to the correct configuration paths for your application type.

> **Note**
> The SDK does not support simultaneously using stateless and stateful guards within the same application at this time. If you need to support both, you will need to create two separate application instances. Support for this is planned for a future release.

### Creating an Auth0 Application

Create a new Auth0 application using [the Auth0 CLI](https://github.com/auth0/):

```bash
auth0 apps create \
  --name "My Laravel Application" \
  --type "regular" \
  --auth-method "post" \
  --callbacks "http://localhost:8000/callback" \
  --logout-urls "http://localhost:8000/login" \
  --reveal-secrets \
  --no-input
```

You will receive a response with details about your new application.

Please make a note of your tenant's **domain** (e.g. `tenant.region.auth0.com`), **client ID**, and **client secret**. These will be required in the configuration step below.

### Creating an Auth0 API

You can create a new Auth0 API using [the Auth0 CLI](https://github.com/auth0/).

In the following command, you can choose any `--name` and `--identifier` values you like. However, the `--identifier` value must be a valid URL, although it does not need to be publicly accessible. Note that the identifier value cannot be changed later.

> **Note**
> The identifier value will be used as the audience claim in your Access Tokens, so it is important to choose a value that will not conflict with other APIs you may be using.

```bash
auth0 apis create \
  --name "My Laravel Application's API" \
  --identifier "https://github.com/auth0/laravel-auth0" \
  --offline-access \
  --no-input
```

You will receive a response with details about your new API.

Please make a note of your new API's **identifier**. This will be required in the configuration step, and will be referred to as the `audience`.

### Configuring the SDK

Open the `.env` file within your Laravel application's root directory, append the lines below to it, and fill in the missing values:

```ini
# Use the `domain` you noted earlier during application creation.
AUTH0_DOMAIN=

# Use the `client id` you noted earlier during application creation.
AUTH0_CLIENT_ID=

# Use the `client_secret` you noted earlier during application creation.
AUTH0_CLIENT_SECRET=

# Use the `identifier` you noted earlier during API creation.
AUTH0_AUDIENCE=

# This should be any sufficiently long, random string.
# You can use `openssl rand -hex 32` to generate an adequate string.
AUTH0_COOKIE_SECRET=
```

If you are building a stateless application, you should also configure the `AUTH0_STRATEGY` environment variable:

```ini
AUTH0_STRATEGY=api
```

### Configuring Your Application

Open your `config/auth.php` file.

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
'providers' => [
    'someProviderName' => [
        'driver' => 'auth0.provider',
        'repository' => \Auth0\Laravel\Auth\User\Repository::class
    ],
],
```

> **Note**
> Please ensure the provider's name (in this case, `someProviderName`) matches the `provider` value in the guard definition.

### Adding Login

> **Note**
> This section only applies to stateful application types.

The SDK provides routing controllers that handle the authentication flow for your application. Add these routes where most appropriate for your configuration. `routes/web.php` is a common location for many Laravel applications.

```php
use Auth0\Laravel\Http\Controller\Stateful\{Login, Logout, Callback};

Route::get('/login', Login::class)->name('login');
Route::get('/logout', Logout::class)->name('logout');
Route::get('/callback', Callback::class)->name('callback');
```

Please ensure requests for these routes are managed by an Auth0 guard configured by your application.

### Protecting Routes

The SDK provides a series of routing middleware to help you secure your application's routes. Any routes you wish to protect should be wrapped in the appropriate middleware.

#### Stateful Applications

**`auth0.authenticate` requires a user to be logged in to access a route.** Other requests will be redirected to the `login` route.

```php
Route::get('/required', function () {
    return view(/* Authenticated */);
})->middleware(['auth0.authenticate']);
```

**`auth0.authenticate.optional` allows anyone to access a route.** It will check if a user is logged in, and will make sure `Auth::user()` is available to the route if so. This is useful when you wish to display different content to logged-in users and guests.

```php
Route::get('/', function () {
    if (Auth::check()) {
        return view(/* Authenticated */)
    }

    return view(/* Guest */)
})->middleware(['auth0.authenticate.optional']);
```

#### Stateless Services

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

**`auth0.authorize.optional` allows anyone to access a route.** It will check if a valid access token is present, and will make sure `Auth::user()` is available to the route if so. This is useful when you wish to return different responses to authenticated and unauthenticated requests.

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

Although we are working toward ensuring the SDK is fully compatible with this feature, we do not recommend using this with our SDK in production until we have full confidence and announced support. Due to the caching behavior of Octane, there is an opportunity for problems we have not fully identified or resolved yet.

Feedback and bug-fix contributions are greatly appreciated as we work toward full support.

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
