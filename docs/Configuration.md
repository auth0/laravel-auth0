# Configuration

> **Note:**  
> This is an addendum to the [README](../README.md) file. Please review that guidance first.

This document covers 'version 2' of the SDK configuration format. You can determine which version you are using by looking for the `Configuration::VERSION_` const returned with the `config/auth0.php` array.

## JSON Configuration (Preferred)

The preferred method of SDK configuration is to use JSON exported from the [Auth0 CLI](https://auth0.com/docs/cli). This allows you to use the CLI to manage your Auth0 configuration, and then export the configuration to JSON for use by the SDK.

The SDK will look for the following files in the project directory, in the order listed:

-   `auth0.json`
-   `auth0.<APP_ENV>.json`
-   `auth0.api.json`
-   `auth0.app.json`
-   `auth0.api.<APP_ENV>.json`
-   `auth0.app.<APP_ENV>.json`

Where `<APP_ENV>` is the value of Laravel's `APP_ENV` environment variable (if set.) Duplicate keys in the files listed above will be overwritten in the order listed.

## Environment Variables

The SDK also supports configuration using environment variables. These can be defined in the host environment, or in a `.env` file in the project directory.

| Variable              | Description                                                                                          |
| --------------------- | ---------------------------------------------------------------------------------------------------- |
| `AUTH0_DOMAIN`        | `String (FQDN)` The Auth0 domain for your tenant.                                                    |
| `AUTH0_CUSTOM_DOMAIN` | `String (FQDN)` The Auth0 custom domain for your tenant, if set.                                     |
| `AUTH0_CLIENT_ID`     | `String` The Client ID for your Auth0 application.                                                   |
| `AUTH0_CLIENT_SECRET` | `String` The Client Secret for your Auth0 application.                                               |
| `AUTH0_AUDIENCE`      | `String (comma-delimited list)` The audiences for your application.                                  |
| `AUTH0_SCOPE`         | `String (comma-delimited list)` The scopes for your application. Defaults to 'openid,profile,email'. |
| `AUTH0_ORGANIZATION`  | `String (comma-delimited list)` The organizations for your application.                              |

The following environment variables are also supported, but should not be adjusted unless you know what you are doing:

| Variable                                   | Description                                                                                                                                                          |
| ------------------------------------------ | -------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `AUTH0_USE_PKCE`                           | Boolean. Whether to use PKCE for the authorization flow. Defaults to `true`.                                                                                         |
| `AUTH0_RESPONSE_MODE`                      | `String` The response mode to use for the authorization flow. Defaults to `query`.                                                                                   |
| `AUTH0_RESPONSE_TYPE`                      | `String` The response type to use for the authorization flow. Defaults to `code`.                                                                                    |
| `AUTH0_TOKEN_ALGORITHM`                    | `String` The algorithm to use for the ID token. Defaults to `RS256`.                                                                                                 |
| `AUTH0_TOKEN_JWKS_URI`                     | `String (URL)` The URI to use to retrieve the JWKS for the ID token. Defaults to `https://<AUTH0_DOMAIN>/.well-known/jwks.json`.                                     |
| `AUTH0_TOKEN_MAX_AGE`                      | `Integer` The maximum age of a token, in seconds. No default value is assigned.                                                                                      |
| `AUTH0_TOKEN_LEEWAY`                       | `Integer` The leeway to use when validating a token, in seconds. Defaults to `60` (1 minute).                                                                        |
| `AUTH0_TOKEN_CACHE`                        | `String (class name)` A PSR-6 class to use for caching JWKS responses.                                                                                               |
| `AUTH0_TOKEN_CACHE_TTL`                    | `Integer` The TTL to use for caching JWKS responses. Defaults to `60` (1 minute).                                                                                    |
| `AUTH0_HTTP_MAX_RETRIES`                   | `Integer` The maximum number of times to retry a failed HTTP request. Defaults to `3`.                                                                               |
| `AUTH0_HTTP_TELEMETRY`                     | `Boolean` Whether to send telemetry data with HTTP requests to Auth0. Defaults to `true`.                                                                            |
| `AUTH0_SESSION_STORAGE`                    | `String (class name)` The `StoreInterface` class to use for storing session data. Defaults to using Laravel's native Sessions API.                                   |
| `AUTH0_SESSION_STORAGE_ID`                 | `String` The namespace to use for storing session data. Defaults to `auth0_session`.                                                                                 |
| `AUTH0_TRANSIENT_STORAGE`                  | `String (class name)` The `StoreInterface` class to use for storing temporary session data. Defaults to using Laravel's native Sessions API.                         |
| `AUTH0_TRANSIENT_STORAGE_ID`               | `String` The namespace to use for storing temporary session data. Defaults to `auth0_transient`.                                                                     |
| `AUTH0_MANAGEMENT_TOKEN`                   | `String` The Management API token to use for the Management API client. If one is not provided, the SDK will attempt to create one for you.                          |
| `AUTH0_MANAGEMENT_TOKEN_CACHE`             | `Integer` A PSR-6 class to use for caching Management API tokens.                                                                                                    |
| `AUTH0_CLIENT_ASSERTION_SIGNING_KEY`       | `String` The key to use for signing client assertions.                                                                                                               |
| `AUTH0_CLIENT_ASSERTION_SIGNING_ALGORITHM` | `String` The algorithm to use for signing client assertions. Defaults to `RS256`.                                                                                    |
| `AUTH0_PUSHED_AUTHORIZATION_REQUEST`       | `Boolean` Whether the SDK should use Pushed Authorization Requests during authentication. Note that your tenant must have this feature enabled. Defaults to `false`. |

## Overriding Automatic Behavior

### Guard Registration

By default, the SDK will register the Authentication and Authorization guards with your Laravel application, as well as a compatible [User Provider](./Users.md).

You can disable this behavior by setting `registerGuards` to false in your `config/auth0.php` file.

To register the guards manually, update the arrays in your `config/auth.php` file to include the following additions:

```php
'guards' => [
    'auth0-session' => [
        'driver' => 'auth0.authenticator',
        'provider' => 'auth0-provider',
        'configuration' => 'web',
    ],
    'auth0-api' => [
        'driver' => 'auth0.authorizer',
        'provider' => 'auth0-provider',
        'configuration' => 'api',
    ],
],

'providers' => [
    'auth0-provider' => [
        'driver' => 'auth0.provider',
        'repository' => 'auth0.repository',
    ],
],
```

### Middleware Registration

By default, the SDK will register the Authentication and Authorization guards within your application's `web` and `api` middleware groups.

You can disable this behavior by setting `registerMiddleware` to false in your `config/auth0.php` file.

To register the middleware manually, update your `app/Http/Kernel.php` file and include the following additions:

```php
protected $middlewareGroups = [
    'web' => [
        // ...
        \Auth0\Laravel\Middleware\AuthenticatorMiddleware::class,
        // ...
    ],

    'api' => [
        // ...
        \Auth0\Laravel\Middleware\AuthorizerMiddleware::class,
        // ...
    ],
];
```

Alternatively, you can assign the guards to specific routes by using the `Auth` facade. For `routes/web.php`, add the following before any routes:

```php
Auth::shouldUse('auth0-session');
```

For `routes/api.php`, add the following before any routes:

```php
Auth::shouldUse('auth0-api');
```

### Authentication Routes

By default, the SDK will register the following routes for authentication:

| Method | URI         | Name       | Controller                                     | Purpose                            |
| ------ | ----------- | ---------- | ---------------------------------------------- | ---------------------------------- |
| `GET`  | `/login`    | `login`    | `Auth0\Laravel\Controllers\LoginController`    | Initiates the authentication flow. |
| `GET`  | `/logout`   | `logout`   | `Auth0\Laravel\Controllers\LogoutController`   | Logs the user out.                 |
| `GET`  | `/callback` | `callback` | `Auth0\Laravel\Controllers\CallbackController` | Handles the callback from Auth0.   |

You can disable this behavior by setting `registerAuthenticationRoutes` to false in your `config/auth0.php` file.

If you've disabled the automatic registration of routes, you must register the routes manually for authentication to work.

```php
use Auth0\Laravel\Controllers\{LoginController, LogoutController, CallbackController};

Route::group(['middleware' => ['guard:auth0-session'], static function (): void {
    Route::get('/login', LoginController::class)->name('login');
    Route::get('/logout', LogoutController::class)->name('logout');
    Route::get('/callback', CallbackController::class)->name('callback');
});
```

Or you can call the SDK Facade's `routes()` method in your `routes/web.php` file:

```php
Auth0::routes();
```

-   These must be registered within the `web` middleware group, as they rely on sessions.
-   Requests must be routed through the SDK's Authenticator guard.
