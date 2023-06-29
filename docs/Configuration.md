# Configuration

- [SDK Configuration](#sdk-configuration)
  - [JSON Configuration Files](#json-configuration-files)
  - [Environment Variables](#environment-variables)
  - [Order of Priority](#order-of-priority)
  - [Default Behavior](#default-behavior)
    - [Guard Registration](#guard-registration)
    - [Middleware Registration](#middleware-registration)
    - [Route Registration](#route-registration)
- [Auth0 Configuration](#auth0-configuration)
  - [Auth0 Applications](#auth0-applications)
    - [Creating Applications with the CLI](#creating-applications-with-the-cli)
    - [Creating Applications Manually](#creating-applications-manually)
    - [Modifying Applications with the CLI](#modifying-applications-using-the-cli)
    - [Modifying Applications Manually](#modifying-applications-manually)
  - [Auth0 APIs](#auth0-apis)
    - [Creating APIs with the CLI](#creating-apis-with-the-cli)
    - [Creating APIs Manually](#creating-apis-manually)
    - [Modifying APIs with the CLI](#modifying-apis-using-the-cli)
    - [Modifying APIs Manually](#modifying-apis-manually)

## SDK Configuration

This guide addresses v2 of the SDK configuration format. You can determine which version you are using by evaluating the constant prepended to the returned array in your application's `config/auth0.php` file, prefixed with `Configuration::VERSION_`. For example:

```php
return Configuration::VERSION_2 + [
    // ...
];
```

If you do not see such a value, you are most likely using an outdated configuration format, and should upgrade by running `php artisan vendor:publish --tag auth0 --force` from your project directory. You will lose any alterations you have made to this file in the process.

### JSON Configuration Files

The preferred method of SDK configuration is to use JSON exported from the [Auth0 CLI](https://auth0.com/docs/cli). This allows you to use the CLI to manage your Auth0 configuration, and then export the configuration to JSON for use by the SDK.

The SDK will look for the following files in the project directory, in the order listed:

- `auth0.json`
- `auth0.<APP_ENV>.json`
- `auth0.api.json`
- `auth0.app.json`
- `auth0.api.<APP_ENV>.json`
- `auth0.app.<APP_ENV>.json`

Where `<APP_ENV>` is the value of Laravel's `APP_ENV` environment variable (if set.) Duplicate keys in the files listed above will be overwritten in the order listed.

### Environment Variables

The SDK also supports configuration using environment variables. These can be defined within the host environment, or using so-called dotenv (`.env`, or `.env.*`) files in the project directory.

| Variable              | Description                                                                                          |
| --------------------- | ---------------------------------------------------------------------------------------------------- |
| `AUTH0_DOMAIN`        | `String (FQDN)` The Auth0 domain for your tenant.                                                    |
| `AUTH0_CUSTOM_DOMAIN` | `String (FQDN)` The Auth0 custom domain for your tenant, if set.                                     |
| `AUTH0_CLIENT_ID`     | `String` The Client ID for your Auth0 application.                                                   |
| `AUTH0_CLIENT_SECRET` | `String` The Client Secret for your Auth0 application.                                               |
| `AUTH0_AUDIENCE`      | `String (comma-delimited list)` The audiences for your application.                                  |
| `AUTH0_SCOPE`         | `String (comma-delimited list)` The scopes for your application. Defaults to 'openid,profile,email'. |
| `AUTH0_ORGANIZATION`  | `String (comma-delimited list)` The organizations for your application.                              |

The following environment variables are supported, but should not be adjusted unless you know what you are doing:

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

### Order of Priority

The SDK collects configuration data from multiple potential sources, in the following order:

- `.auth0.json` files
- `.env` (dotenv) files
- Host environment variables

> **Note:**  
> In the filenames listed below, `%APP_ENV%` is replaced by the application's configured `APP_ENV` environment variable, if one is set.

It begins by loading matching JSON configuration files from the project's root directory, in the following order:

- `.auth0.json`
- `.auth0.%APP_ENV%.json`
- `.auth0.api.json`
- `.auth0.app.json`
- `.auth0.api.%APP_ENV%.json`
- `.auth0.app.%APP_ENV%.json`

It then loads configuration data from available `.env` (dotenv) configuration files, in the following order.

- `.env`
- `.env.auth0`
- `.env.%APP_ENV%`
- `.env.%APP_ENV%.auth0`

Finally, it loads environment variables from the host environment.

Duplicate configuration data is overwritten by the value from the last source loaded. For example, if the `AUTH0_DOMAIN` environment variable is set in both the `.env` file and the host environment, the value from the host environment will be used.

Although JSON configuration keys are different from their associated environment variable counterparts, these are translated automatically by the SDK. For example, the `domain` key in the JSON configuration files is translated to the `AUTH0_DOMAIN` environment variable.

### Default Behavior

#### Guard Registration

By default, the SDK will register the Authentication and Authorization guards with your Laravel application, as well as a compatible [User Provider](./Users.md).

You can disable this behavior by setting `registerGuards` to `false` in your `config/auth0.php` file.

```php
return Configuration::VERSION_2 + [
    'registerGuards' => false,
    // ...
];
```

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

#### Middleware Registration

By default, the SDK will register the Authentication and Authorization guards within your application's `web` and `api` middleware groups.

You can disable this behavior by setting `registerMiddleware` to `false` in your `config/auth0.php` file.

```php
return Configuration::VERSION_2 + [
    'registerMiddleware' => false,
    // ...
];
```

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

#### Route Registration

By default, the SDK will register the following routes for authentication:

| Method | URI         | Name       | Controller                                     | Purpose                            |
| ------ | ----------- | ---------- | ---------------------------------------------- | ---------------------------------- |
| `GET`  | `/login`    | `login`    | `Auth0\Laravel\Controllers\LoginController`    | Initiates the authentication flow. |
| `GET`  | `/logout`   | `logout`   | `Auth0\Laravel\Controllers\LogoutController`   | Logs the user out.                 |
| `GET`  | `/callback` | `callback` | `Auth0\Laravel\Controllers\CallbackController` | Handles the callback from Auth0.   |

You can disable this behavior by setting `registerAuthenticationRoutes` to `false` in your `config/auth0.php` file.

```php
return Configuration::VERSION_2 + [
    'registerAuthenticationRoutes' => false,
    // ...
];
```

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

- These must be registered within the `web` middleware group, as they rely on sessions.
- Requests must be routed through the SDK's Authenticator guard.

## Auth0 Configuration

The following guidance is provided to help you configure your Auth0 tenant for use with the SDK. It is not intended to be a comprehensive guide to configuring Auth0. Please refer to the [Auth0 documentation](https://auth0.com/docs) for more information.

### Auth0 Applications

#### Creating Applications with the CLI

Use the CLI's `apps create` command to create a new Auth0 Application:

```shell
auth0 apps create \
  --name "My Laravel Application" \
  --type "regular" \
  --auth-method "post" \
  --callbacks "http://localhost:8000/callback" \
  --logout-urls "http://localhost:8000" \
  --reveal-secrets \
  --no-input
```

If you are configuring the SDK for this application, make note of the `client_id` and `client_secret` values returned by the command. Follow the guidance in the [configuration guide](#configuration) to configure the SDK using these values.

The following parameters used in this example are of note:

- `--type` - The [application type](https://auth0.com/docs/get-started/applications).
  - For Laravel applications, this should always be set to `regular`.
- `--auth-method` - This represents the 'Token Endpoint Authentication Method' used for authentication.
  - For Laravel applications, this should always be set to `post`.
- `--callbacks` - The callback URLs to use for authentication. 
  - In development, this should be set to `http://localhost:8000/callback` or as appropriate.
  - In production, adjust this value to match your application's Internet-accessible URL for its`/callback`` route.
  - This value can be a comma-separated list of URLs.
- `--logout-urls` - The logout URLs to use for authentication.
  - In development, this should be set to `http://localhost:8000` or as appropriate.
  - In production, adjust this value to match where your application redirects end users after logging out. The value should be an Internet-accessible URL.
  - This value can be a comma-separated list of URLs.

Please refer to the [CLI documentation](https://auth0.github.io/auth0-cli/auth0_apps_create.html) for additional information on the `apps create` command.

#### Modifying Applications using the CLI

Use the CLI's `apps update` command to create a new Auth0 API:

```shell
auth0 apps update %CLIENT_ID% \
  --name "My Updated Laravel Application" \
  --callbacks "https://production/callback,http://localhost:8000/callback" \
  --logout-urls "https://production/logout,http://localhost:8000" \
  --no-input
```

Substitute `%CLIENT_ID%` with your application's Client ID. Depending on how you configured the SDK, this value can be found:

- In the `.auth0.app.json` file in your project's root directory, as the `client_id` property value.
- In the `.env` file in your project's root directory, as the `AUTH0_CLIENT_ID` property value.
- As the `AUTH0_CLIENT_ID` environment variable.
- Evaluating the output from the CLI's `apps list` command.

Please refer to the [CLI documentation](https://auth0.github.io/auth0-cli/auth0_apps_update.html) for additional information on the `apps update` command.

#### Creating Applications Manually

1. Log in to your [Auth0 Dashboard](https://manage.auth0.com/).
2. Click the **Applications** menu item in the left navigation bar.
3. Click the **Create Application** button.
4. Enter a name for your application.
5. Select **Regular Web Applications** as the application type.
6. Click the **Create** button.
7. Click the **Settings** tab.
8. Set the **Token Endpoint Authentication Method** to `POST`.
9. Set the **Allowed Callback URLs** to `http://localhost:8000/callback` or as appropriate.
10. Set the **Allowed Logout URLs** to `http://localhost:8000` or as appropriate.
11. Click the **Save Changes** button.

#### Modifying Applications Manually

1. Log in to your [Auth0 Dashboard](https://manage.auth0.com/).
2. Click the **Applications** menu item in the left navigation bar.
3. Click the name of the application you wish to modify.
4. Click the **Settings** tab.
5. Modify the properties you wish to update as appropriate.
6. Click the **Save Changes** button.

### Auth0 APIs

#### Creating APIs with the CLI

Use the CLI's `apis create` command to create a new Auth0 API:

```shell
auth0 apis create \
  --name "My Laravel Application API" \
  --identifier "https://github.com/auth0/laravel-auth0" \
  --offline-access \
  --no-input
```

If you are configuring the SDK for this API, make note of the `identifier` you used here. Follow the guidance in the [configuration guide](#configuration) to configure the SDK using this value.

The following parameters are of note:

- `--identifier` - The [unique identifier](https://auth0.com/docs/get-started/apis/api-settings#general-settings) for your API, sometimes referred to as the `audience`. This can be any value you wish, but it must be unique within your account. It cannot be changed later.
- `--offline-access` - This enables the use of [Refresh Tokens](https://auth0.com/docs/tokens/refresh-tokens) for your API. This is not required for the SDK to function.

Please refer to the [CLI documentation](https://auth0.github.io/auth0-cli/auth0_apis_create.html) for additional information on the `apis create` command.

#### Modifying APIs using the CLI

Use the CLI's `apis update` command to create a new Auth0 API:

```shell
auth0 apis update %IDENTIFIER% \
  --name "My Updated Laravel Application API" \
  --token-lifetime 6100 \
  --offline-access=false \
  --scopes "letter:write,letter:read" \
  --no-input
```

Substitute `%IDENTIFIER%` with your API's unique identifier.

Please refer to the [CLI documentation](https://auth0.github.io/auth0-cli/auth0_apis_update.html) for additional information on the `apis update` command.

#### Creating APIs Manually

1. Log in to your [Auth0 Dashboard](https://manage.auth0.com/).
2. Click the **APIs** menu item in the left navigation bar.
3. Click the **Create API** button.
4. Enter a name for your API.
5. Enter a unique identifier for your API. This can be any value you wish, but it must be unique within your account. It cannot be changed later.
6. Click the **Create** button.

#### Modifying APIs Manually

1. Log in to your [Auth0 Dashboard](https://manage.auth0.com/).
2. Click the **APIs** menu item in the left navigation bar.
3. Click the name of the API you wish to modify.
4. Modify the properties you wish to update as appropriate.
5. Click the **Save Changes** button.

Additional information on Auth0 application settings [can be found here](https://auth0.com/docs/get-started/applications/application-settings).
