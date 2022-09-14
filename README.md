# Auth0 Laravel SDK

[![Latest Stable Version](https://img.shields.io/packagist/v/auth0/login?label=stable)](https://packagist.org/packages/auth0/laravel-auth0)
[![Latest Version](https://img.shields.io/packagist/v/auth0/login?include_prereleases&label=latest)](https://packagist.org/packages/auth0/laravel-auth0)
[![Supported PHP Versions](https://img.shields.io/packagist/php-v/auth0/login)](https://packagist.org/packages/auth0/laravel-auth0)
[![License](https://img.shields.io/packagist/l/auth0/login)](https://packagist.org/packages/auth0/laravel-auth0)

This SDK helps you integrate your [Laravel](https://laravel.com/) application with [Auth0](https://auth0.com/) to achieve single sign-on with a few simple steps. The SDK also provides an easy method of integration all the functionality of the underlying [Auth0-PHP](https://github.com/auth0/auth0-PHP) inside your Laravel application, including all types of authentication, authorization of API endpoints, and issuing Management API calls.

- [Requirements](#requirements)
- [Usage](#usage)
  - [Getting Started](#getting-started)
  - [Installation](#installation)
  - [Configuration the SDK](#configuration-the-sdk)
    - [Regular Web Applications](#regular-web-applications)
    - [Backend API Applications](#backend-api-applications)
    - [Additional Options](#additional-options)
  - [Configure your Application](#configure-your-application)
  - [Authentication Routes](#authentication-routes)
  - [Protecting Routes with Middleware](#protecting-routes-with-middleware)
    - [Regular Web Applications](#regular-web-applications-1)
    - [Backend API Applications](#backend-api-applications-1)
  - [Custom User Models and Repositories](#custom-user-models-and-repositories)
    - [Creating a Custom User Model](#creating-a-custom-user-model)
    - [Creating a Custom User Repository](#creating-a-custom-user-repository)
    - [Using a Custom User Repository](#using-a-custom-user-repository)
  - [Authorizing HTTP Tests](#authorizing-http-tests)
  - [Octane](#octane)
- [Documentation](#documentation)
- [Contributing](#contributing)
- [Support + Feedback](#support--feedback)
- [Vulnerability Reporting](#vulnerability-reporting)
- [What is Auth0?](#what-is-auth0)
- [License](#license)

## Requirements

| SDK Version | Laravel Version¹ | PHP Version² | Support Ends³ |
|-------------|------------------|--------------|---------------|
| 7           | 9                | 8.1          | Feb 2024      |
|             |                  | 8.0          | Nov 2023      |
|             | 8                | 8.1          | Jan 2023      |
|             |                  | 8.0          | Jan 2023      |
|             |                  | 7.4          | Nov 2022      |
| 6⁴          | 8                | 8.1          | Jan 2023      |
|             |                  | 8.0          | Jan 2023      |
|             |                  | 7.4          | Nov 2022      |
|             | 6 (LTS)          | 8.0          | Sep 2022      |
|             |                  | 7.4          | Sep 2022      |

¹ This library follows the [Laravel release support schedule](https://laravel.com/docs/releases#support-policy). We do not support framework versions after they stop receiving security fixes from Laravel.

² This library follows the [PHP release support schedule](https://www.php.net/supported-versions.php). We do not support runtime versions after they stop receiving security fixes from the PHP Group.

³ Our support windows are determined by the Laravel and PHP Group support schedules, and support ends when either the Laravel framework or PHP runtime outlined above stop receiving security fixes, whichever comes first.

⁴ With the release of Laravel SDK v7, v6 is now in bug-fix only mode. Please migrate to v7 to continue to receive feature enhancements.

## Usage

### Getting Started

- Create a [free Auth0 account](https://auth0.com/signup) and register an [Application](https://auth0.com/docs/applications).
- If you do not already have one, [prepare a Laravel project](https://laravel.com/docs/master/installation).

### Installation

The supported method of SDK installation is through [Composer](https://getcomposer.org/). From your terminal shell, `cd` into your project directory and issue the following command:

```bash
composer require auth0/login
```

### Configuration the SDK

Use the Laravel `vendor:publish` command to import the configuration file into your application:

```sh
php artisan vendor:publish --tag=auth0-config
```

Now edit your `.env` file and add Auth0 tenants details for your project, depending on the type of application you're building:

#### Regular Web Applications

```sh
# The URL of your Auth0 tenant domain
# You'll find this in your Auth0 Application's settings page.
AUTH0_DOMAIN=...

# Your Auth0 application's Client ID
# You'll find this in your Auth0 Application's settings page.
AUTH0_CLIENT_ID=...

# Your Auth0 application's Client ID
# You'll find this in your Auth0 Application's settings page.
AUTH0_CLIENT_SECRET=...

# Your Auth0 Custom API identifier/audience.
# You'll find this in your Custom API's settings page.
AUTH0_AUDIENCE=...

# Authentication callback URI, as defined in your Auth0 Application settings.
# (Update this as appropriate for your application's location.)
# (You must configure this in your Auth0 Application's settings page as well!)
AUTH0_REDIRECT_URI=http://localhost:3000/auth0/callback
```

#### Backend API Applications

These are applications that accept an Access Token through the 'Authorization' header of a request.

```sh
# This tells the Auth0 Laravel SDK about your use case to customize its behavior.
# The 'api' strategy is used for backend API applications like we're building here.
# See: https://github.com/auth0/auth0-PHP/blob/main/README.md#configuration-strategies
AUTH0_STRATEGY=api

# The URL of your Auth0 tenant domain
# You'll find this in your Auth0 Application's settings page.
AUTH0_DOMAIN=...

# Your Auth0 application's Client ID
# You'll find this in your Auth0 Application's settings page.
AUTH0_CLIENT_ID=...

# Your Auth0 Custom API identifier/audience.
# You'll find this in your Custom API's settings page.
AUTH0_AUDIENCE=...
```

#### Additional Options

The default configuration provided by the Auth0 Laravel SDK is intentionally limited and designed to support only the most common types of applications. More complex applications may require more robust configuration customizations available in the underlying Auth0-PHP SDK. You can add support for more configuration options by modifying your `config/auth0.php` and `.env` files. A complete list of configuration options are available [from the Auth0-PHP SDK README](https://github.com/auth0/auth0-PHP/blob/main/README.md#configuration-options).

### Configure your Application

Integrating the SDK's Guard requires some small changes to your `config\auth.php` file.

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

Finally, find the `providers` section, and add `auth0` there as well:
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

### Authentication Routes

The SDK offers a number of convenience route controllers to ease supporting authentication in regular web application (that is, an application that handles end users logging in and out).

```php
Route::get('/login', \Auth0\Laravel\Http\Controller\Stateful\Login::class)->name('login');
Route::get('/logout', \Auth0\Laravel\Http\Controller\Stateful\Logout::class)->name('logout');
Route::get('/auth0/callback', \Auth0\Laravel\Http\Controller\Stateful\Callback::class)->name('auth0.callback');
```

These routes will automatically handle your regular web application's authentication flow for you.

### Protecting Routes with Middleware

The Auth0 Laravel SDK includes a number of middleware that simplify either authenticating (regular web applications) or authorizing (backend api applications) your Laravel routes, depending on the type of application you're building.

#### Regular Web Applications

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

Note that the `example.user.template` and `example.guest.templates` views are just examples and are not part of the SDK; replace these as appropriate for your app.

#### Backend API Applications

These are applications that accept an Access Token through the 'Authorization' header of a request.

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

### Custom User Models and Repositories

In Laravel, a User Repository is an interface that sits between your authentication source (Auth0) and core Laravel authentication services. It allows you to shape and manipulate the user model and it's data as you need to.

For example, Auth0's unique identifier is a `string` in the format `auth0|123456abcdef`. If you were to attempt to persist a user to many traditional databases you'd likely encounter an error as, by default, a unique identifier is often exected to be an `integer` rather than a `string` type. A custom user model and repository is a great way to address integration challenges like this.

#### Creating a Custom User Model

Let's setup a custom user model for our application. To do this, let's create a file at `app/Auth/Models/User.php` within our Laravel project. This new class needs to implement the `Illuminate\Contracts\Auth\Authenticatable` interface to be compatible with Laravel's Guard API and this SDK. It must also implement either `Auth0\Laravel\Contract\Model\Stateful\User` or `Auth0\Laravel\Contract\Model\Stateless\User` depending on your application's needs. For example:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Auth0\Laravel\Contract\Model\Stateful\User as StatefulUser;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends \Illuminate\Database\Eloquent\Model implements StatefulUser, AuthenticatableUser
{
    use HasFactory, Notifiable, Authenticatable;

    /**
     * The primary identifier for the user.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];
}
```

#### Creating a Custom User Repository

Now let's create a custom user repository for your application which will return the new new custom model. To do this, create the file `app/Auth/CustomUserRepository.php`. This new class must implment the `Auth0\Laravel\Contract\Auth\User\Repository` interface. This new repository takes in user data returned from Auth0's API, applies it to the `App\Models\User` custom user model created in the previous step, and returns it for use throughout your application.

```php
<?php

declare(strict_types=1);

namespace App\Auth;

class CustomUserRepository implements \Auth0\Laravel\Contract\Auth\User\Repository
{
    public function fromSession(
        array $user
    ): ?\Illuminate\Contracts\Auth\Authenticatable {
        return new \App\Models\User([
            'id' => 'just_a_random_example|' . $user['sub'] ?? $user['user_id'] ?? null,
            'name' => $user['name'],
            'email' => $user['email']
        ]);
    }

    public function fromAccessToken(
        array $user
    ): ?\Illuminate\Contracts\Auth\Authenticatable {
        // Simliar to above. Used for stateless application types.
        return null;
    }
}
```

#### Using a Custom User Repository

Finally, update your application's `config/auth.php` file. Within the Auth0 provider, assign a custom `repository` value pointing to your new custom user provider class. For example:

```php
    'providers' => [
        //...

        'auth0' => [
            'driver' => 'auth0',
            'repository' => App\Auth\CustomUserRepository::class
        ],
    ],
```

### Authorizing HTTP Tests

If your application does contain HTTP tests which access routes that are protected by the `auth0.authorize` middleware, you can use the trait `Auth0\Laravel\Traits\ActingAsAuth0User` in your tests, which will give you a helper method `actingAsAuth0User(array $attributes=[])` simmilar to Laravels `actingAs` method, that allows you to fake beeing authenticated as a Auth0 user.

The argument `attributes` is optional and you can use it to set any auth0 specific user attributes like scope, sub, azp, iap and so on. If no attributes are set, some default values are used.

#### Example with a scope protected route
Let's assume you have a route like the following, that is protected by the scope `read:messages`:
```php
Route::get('/api/private-scoped', function () {
    return response()->json([
        'message' => 'Hello from a private endpoint! You need to be authenticated and have a scope of read:messages to see this.',
        'authorized' => Auth::check(),
        'user' => Auth::check() ? json_decode(json_encode((array) Auth::user(), JSON_THROW_ON_ERROR), true) : null,
    ], 200, [], JSON_PRETTY_PRINT);
})->middleware(['auth0.authorize:read:messages']);
```

To be able to test the route from above, the implementation of your test would have to look like this:
```php
use Auth0\Laravel\Traits\ActingAsAuth0User;

public function test_readMessages(){
    $response = $this->actingAsAuth0User([
        "scope"=>"read:messages"
    ])->getJson("/api/private-scoped");

    $response->assertStatus(200);
}
```

### Octane

Octane compatibility is currently considered experimental and unsupported.

Although we are working toward ensuring the SDK is fully compatible with this performance-enhancing technique backed by Open Swoole and RoadRunner, we do not recommend using this feature with our SDK in production until we have full confidence and greenlit support. Due to the aggressive changes Octane makes to Laravel's behavior, there is opportunity for problems we haven't fully identified or resolved yet.

Feedback and bug fix contributions are greatly appreciated as we work on Octane support.

## Documentation

We provide a number of sample apps that demonstrate common use cases, to help you get started using this SDK:

- [Web Application Authentication](https://auth0.com/docs/quickstart/webapp/laravel/) ([GitHub repo](https://github.com/auth0-samples/auth0-laravel-php-web-app))
- [Backend API Authorization](https://auth0.com/docs/quickstart/backend/laravel/) ([GitHub repo](https://github.com/auth0-samples/auth0-laravel-api-samples))

## Contributing

We appreciate feedback and contribution to this repo! Before you get started, please see the following:

- [Auth0's Contribution guidelines](https://github.com/auth0/.github/blob/master/CONTRIBUTING.md)
- [Auth0's Code of Conduct](https://github.com/auth0/open-source-template/blob/master/CODE-OF-CONDUCT.md)

## Support + Feedback

- The [Auth0 Community](https://community.auth0.com/) is a valuable resource for asking questions and finding answers, staffed by the Auth0 team and a community of enthusiastic developers
- For code-level support (such as feature requests and bug reports), we encourage you to [open issues](https://github.com/auth0/laravel-auth0/issues) here on our repo
- For customers on [paid plans](https://auth0.com/pricing/), our [support center](https://support.auth0.com/) is available for opening tickets with our knowledgeable support specialists

Further details about our support solutions are [available on our website.](https://auth0.com/docs/support)

## Vulnerability Reporting

Please do not report security vulnerabilities on the public GitHub issue tracker. The [Responsible Disclosure Program](https://auth0.com/whitehat) details the procedure for disclosing security issues.

## What is Auth0?

Auth0 helps you to:

- Add authentication with [multiple authentication sources](https://docs.auth0.com/identityproviders), either social like Google, Facebook, Microsoft, LinkedIn, GitHub, Twitter, Box, Salesforce (amongst others), or enterprise identity systems like Windows Azure AD, Google Apps, Active Directory, ADFS or any SAML Identity Provider.
- Add authentication through more traditional **[username/password databases](https://docs.auth0.com/mysql-connection-tutorial)**.
- Add support for [passwordless](https://auth0.com/passwordless) and [multi-factor authentication](https://auth0.com/docs/mfa).
- Add support for [linking different user accounts](https://docs.auth0.com/link-accounts) with the same user.
- Analytics of how, when, and where users are logging in.
- Pull data from other sources and add it to the user profile through [JavaScript rules](https://docs.auth0.com/rules).

[Why Auth0?](https://auth0.com/why-auth0)

## License

The Auth0 Laravel SDK is open source software licensed under [the MIT license](https://opensource.org/licenses/MIT). See the [LICENSE](LICENSE.txt) file for more info.

[![FOSSA Status](https://app.fossa.com/api/projects/git%2Bgithub.com%2Fauth0%2Flaravel-auth0.svg?type=large)](https://app.fossa.com/projects/git%2Bgithub.com%2Fauth0%2Flaravel-auth0?ref=badge_large)
