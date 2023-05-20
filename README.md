![laravel-auth0](https://cdn.auth0.com/website/sdks/banners/laravel-auth0-banner.png)

Laravel SDK for [Auth0](https://auth0.com) Authentication and Management APIs.

[![Package](https://img.shields.io/packagist/dt/auth0/login)](https://packagist.org/packages/auth0/login)
![Build Status](https://img.shields.io/github/checks-status/auth0/laravel-auth0/main)
[![Coverage](https://img.shields.io/codecov/c/github/auth0/laravel-auth0/main)](https://codecov.io/gh/auth0/laravel-auth0)
[![License](https://img.shields.io/packagist/l/auth0/login)](https://doge.mit-license.org/)

:books: [Documentation](#documentation) - :rocket: [Getting Started](#getting-started) - :speech_balloon: [Feedback](#feedback)

## Documentation

Laravel SDK Quickstarts:

-   [Session-based Authentication](https://auth0.com/docs/quickstart/webapp/laravel) ([GitHub](https://github.com/auth0-samples/laravel))
-   [Token-based Authorization](https://auth0.com/docs/quickstart/backend/laravel) ([GitHub](https://github.com/auth0-samples/laravel))

Laravel SDK Documentation:

-   [Getting Started](./README.md#getting-started) — Installing and configuring the SDK.
-   [Examples](./EXAMPLES.md) — Answers and solutions for common questions and scenarios.
-   Additional Reference:
    -   [docs/Installation](./docs/Installation.md) — Installing the SDK and generating configuration files.
    -   [docs/Configuration](./docs/Configuration.md) — Configuring the SDK using JSON files or environment variables.
    -   [docs/Management](./docs/Management.md) — Using the SDK to call the [Management API](https://auth0.com/docs/api/management/v2).
    -   [docs/Users](./docs/Users.md) — Extending the SDK to support persistent storage and [Eloquent](https://laravel.com/docs/eloquent).
    -   [docs/Events](./docs/Events.md) — Hooking into SDK [events](https://laravel.com/docs/events) to respond to specific actions.

Auth0:

-   [Documentation](https://www.auth0.com/docs)
-   [Management API Explorer](https://auth0.com/docs/api/management/v2)
-   [Authentication API Explorer](https://auth0.com/docs/api/authentication)

## Getting Started

### Requirements

You will need to use a [supported version](#support-policy) of PHP and Laravel:

| Laravel | PHP  |
| ------- | ---- |
| 10      | 8.1+ |
| 9       | 8.0+ |

You'll also need an [Auth0 account](https://auth0.com/signup), as well as [Composer](https://getcomposer.org/) and the [Auth0 CLI](https://github.com/auth0/auth0-cli).

### SDK Installation

Run the following command within your project directory to install the [Auth0 Laravel SDK](https://github.com/auth0/laravel-auth0):

```shell
composer require auth0/login:^7.8 --update-with-all-dependencies
```

Then generate an SDK configuration file for your application:

```shell
php artisan vendor:publish --tag auth0
```

### SDK Configuration

> **Note**
> If you prefer to use environment variables to configure the SDK, please see [docs/Installation](./docs/Installation.md) for guidance.

Run the following command from your project directory to download the [Auth0 CLI](https://github.com/auth0/auth0-cli):

```shell
curl -sSfL https://raw.githubusercontent.com/auth0/auth0-cli/main/install.sh | sh -s -- -b .
```

Then authenticate the CLI with your Auth0 account:

```shell
./auth0 login
```

Next, create a new application with Auth0:

```shell
./auth0 apps create \
  --name "My Laravel Application" \
  --type "regular" \
  --auth-method "post" \
  --callbacks "http://localhost:8000/callback" \
  --logout-urls "http://localhost:8000" \
  --reveal-secrets \
  --no-input \
  --json > .auth0.app.json
```

You should also create a new API:

```shell
./auth0 apis create \
  --name "My Laravel Application API" \
  --identifier "https://github.com/auth0/laravel-auth0" \
  --offline-access \
  --no-input \
  --json > .auth0.api.json
```

This produces two files in your project directory that configure the SDK.

As these files contain credentials it's important to treat these as sensitive. You should ensure you do not commit these to version control. If you're using Git, you should add them to your `.gitignore` file:

```bash
echo ".auth0.*.json" >> .gitignore
```

### Authentication

The SDK automatically registers all the necessary facilities within the `web` middleware group for your users to authenticate with your application. These routes are:

| Route       | Purpose                            |
| ----------- | ---------------------------------- |
| `/login`    | Initiates the authentication flow. |
| `/logout`   | Logs the user out.                 |
| `/callback` | Handles the callback from Auth0.   |

See [docs/Configuration](./docs/Configuration.md) for guidance on disabling automatic registration, and manually registering these facilities.

### Access Control

The SDK automatically registers its authentication and authorization guards into the standard `web` and `api` middleware groups for your Laravel application, respectively.

See [docs/Configuration](./docs/Configuration.md) for guidance on disabling this automatic registration, and manually registering the guards.

You can use the Auth0 SDK's authentication guard to restrict access to your application's routes.

To require the requesting party to be authenticated (routes using the `web` middleware defined in `routes/web.php`) or authorized (routes using the `api` middleware defined in `routes/api.php`), you can use Laravel's `auth` middleware:

```php
Route::get('/private', function () {
  return response('Welcome! You are logged in.');
})->middleware('auth');
```

You can also require the requestor to have specific [permissions](https://auth0.com/docs/manage-users/access-control/rbac)](https://auth0.com/docs/manage-users/access-control/rbac) by combining this with Laravel's `can` middleware:

```php
Route::get('/scope', function () {
    return response('You have the `read:messages` permissions, and can therefore access this resource.');
})->middleware('auth')->can('read:messages');
```

> **Note**
> Permissions need RBAC to be enabled on [your Auth0 API's settings](https://manage.auth0.com/#/apis).

### User and Token Information

When requests are made to your application, the SDK will automatically attempt to authenticate or authorize the requestor, depending on the type of route. Information about the requesting party is expressed through the `user()` method of Laravel's `Auth` Facade, or the `auth()` helper function.

For routes using the `web` middleware defined in `routes/web.php`, the `user()` method will return profile information for the authenticated user.

```php
Route::get('/', function () {
  if (! auth()->check()) {
    return response('You are not logged in.');
  }

  $user = auth()->user();
  $name = $user->name ?? 'User';
  $email = $user->email ?? '';

  return response("Hello {$name}! Your email address is {$email}.");
});;
```

For routes using the `api` middleware defined in `routes/api.php`, the `user()` method will return details about the access token.

```php
Route::get('/', function () {
  if (! auth()->check()) {
    return response()->json([
      'message' => 'You did not provide a valid token.',
    ]);
  }

  return response()->json([
    'message' => 'Your token is valid; you are authorized.',
    'id' => auth()->id(),
    'token' => auth()?->user()?->getAttributes(),
  ]);
});
```

### Management API

You can update user information using the [Auth0 Management API](https://github.com/auth0/laravel-auth0/blob/main/docs/Management.md). All Management endpoints are accessible through the SDK's `management()` method.

**Before making Management API calls you must enable your application to communicate with the Management API.** This can be done from the [Auth0 Dashboard's API page](https://manage.auth0.com/#/apis/), choosing `Auth0 Management API`, and selecting the 'Machine to Machine Applications' tab. Authorize your Laravel application, and then click the down arrow to choose the scopes you wish to grant.

For the following example, in which we will update a user's metadata and assign a random favorite color, you should grant the `read:users` and `update:users` scopes. A list of API endpoints and the required scopes can be found in [the Management API documentation](https://auth0.com/docs/api/management/v2).

```php
use Auth0\Laravel\Facade\Auth0;

Route::get('/colors', function () {
  $endpoint = Auth0::management()->users();

  $colors = ['red', 'blue', 'green', 'black', 'white', 'yellow', 'purple', 'orange', 'pink', 'brown'];

  $endpoint->update(
    id: auth()->id(),
    body: [
        'user_metadata' => [
            'color' => $colors[random_int(0, count($colors) - 1)]
        ]
    ]
  );

  $metadata = $endpoint->get(auth()->id());
  $metadata = Auth0::json($metadata);

  $color = $metadata['user_metadata']['color'] ?? 'unknown';
  $name = auth()->user()->name;

  return response("Hello {$name}! Your favorite color is {$color}.");
})->middleware('auth');
```

A quick reference guide of all the SDK's Management API methods is [available here](https://github.com/auth0/laravel-auth0/blob/main/docs/Management.md).

## Gates and Policies

The SDK supports [Laravel's Authorization API](https://laravel.com/docs/authorization#main-content) and provides several convenient pre-built [Gates](https://laravel.com/docs/authorization#gates) for common tasks.

| Gate         | Facade Usage                                  | Middleware Usage                                           | Purpose                                                                                                                                                                                                                    |
| ------------ | --------------------------------------------- | ---------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `scope`      | `Gate::check('scope', 'email');`              | `Route::get(/*...*/)->can('scope', 'email');`              | Determine if the user's access token has a specified [scope](https://auth0.com/docs/get-started/apis/scopes).                                                                                                              |
| `permission` | `Gate::check('permission', 'read:messages');` | `Route::get(/*...*/)->can('permission', 'read:messages');` | Determine if the user's access token has a specified [permission](https://auth0.com/docs/manage-users/access-control/rbac). This requires RBAC be enabled on [your Auth0 API's settings](https://manage.auth0.com/#/apis). |
| `*:*`        | `Gate::check('read:messages');`               | `Route::get(/*...*/)->can('read:messages');`               | A convenience alias for `permission` (described above), you can supply any permission string in the colon-delimited `ability:context` syntax.                                                                              |

Using these gates, you can easily authorize users or access tokens to perform actions in your application. Your application can use Laravel's [Policies API](https://laravel.com/docs/authorization#creating-policies) in combination with these gates to further simplify authorization.

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
