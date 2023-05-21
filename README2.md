![laravel-auth0](https://cdn.auth0.com/website/sdks/banners/laravel-auth0-banner.png)

<p align="right">
<a href="https://github.com/auth0/laravel-auth0/actions"><img src="https://github.com/auth0/laravel-auth0/actions/workflows/main.yml/badge.svg?event=push" alt="Build Status"></a>
<a href="https://codecov.io/gh/auth0/laravel-auth0"><img src="https://codecov.io/gh/auth0/laravel-auth0/branch/main/graph/badge.svg?token=vEwn6TPADf" alt="Code Coverage"></a>
<a href="https://packagist.org/packages/auth0/laravel-auth0"><img src="https://img.shields.io/packagist/dt/auth0/login" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/auth0/login"><img src="https://img.shields.io/packagist/l/auth0/login" alt="License"></a>
</p>

## Requirements

Your application must use a [supported Laravel version](https://laravelversions.com/en), and your environment must run a [supported PHP version](https://www.php.net/supported-versions.php). We do not support versions of Laravel or PHP that are no longer supported by their maintainers.

| SDK  | Laravel | PHP  | Supported Until |
| ---- | ------- | ---- | --------------- |
| 7.5+ | 10      | 8.2+ | Feb 2025        |
|      |         | 8.1+ | Nov 2024        |
| 7.0+ | 9       | 8.2+ | Feb 2024        |
|      |         | 8.1+ | Feb 2024        |
|      |         | 8.0+ | Nov 2023        |

You will also need [Composer](https://getcomposer.org/) and an [Auth0 account](https://auth0.com/signup).

## Installation

<details>

<summary>Using <b>Quickstart</b> (Fastest)</summary>

-   Run the following command to create a new Laravel 9 project pre-configured with the SDK:

    ```shell
    composer create-project auth0-samples/laravel auth0-laravel-app
    ```

---

</details>

<details>
<summary>Using <b>Composer</b></summary>

1.  Run the following command in your project directory to install the SDK:

    ```shell
    composer require auth0/login:^7.8 --update-with-all-dependencies
    ```

2.  Generate an SDK configuration file for your application:

    ```shell
    php artisan vendor:publish --tag auth0
    ```

</details>

## Configuration

<details>
<summary>Using <b>JSON</b> (Fastest)</summary>

1. Download the [Auth0 CLI](https://github.com/auth0/auth0-cli) to your project directory:

    > **Note**  
    > If you are using the Quickstart, skip to the next step.

    ```shell
    curl -sSfL https://raw.githubusercontent.com/auth0/auth0-cli/main/install.sh | sh -s -- -b .
    ```

2. Authenticate with your Auth0 account:

    ```shell
    ./auth0 login
    ```

3. Register a new application with Auth0:

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

4. Register a new API with Auth0

    ```shell
    ./auth0 apis create \
        --name "My Laravel Application API" \
        --identifier "https://github.com/auth0/laravel-auth0" \
        --offline-access \
        --no-input \
        --json > .auth0.api.json
    ```

5. Add the newly created JSON files to `.gitignore`, as they contain sensitive credentials:

    ```bash
    echo ".auth0.*.json" >> .gitignore
    ```

---

</details>

<details>
<summary>Using <b>Environment Variables</b></summary>

1. Download the [Auth0 CLI](https://github.com/auth0/auth0-cli) to your project directory:

    > **Note**  
    > If you are using the Quickstart, skip to the next step.

    ```shell
    curl -sSfL https://raw.githubusercontent.com/auth0/auth0-cli/main/install.sh | sh -s -- -b .
    ```

2. Authenticate with your Auth0 account:

    ```shell
    ./auth0 login
    ```

3. Register a new application with Auth0:

    ```shell
    ./auth0 apps create \
        --name "My Laravel Application" \
        --type "regular" \
        --auth-method "post" \
        --callbacks "http://localhost:8000/callback" \
        --logout-urls "http://localhost:8000" \
        --reveal-secrets \
        --no-input
    ```

    Make a note of the `client_id` and `client_secret` values in the output.

4. Register a new API with Auth0

    ```shell
    ./auth0 apis create \
        --name "My Laravel Application API" \
        --identifier "https://github.com/auth0/laravel-auth0" \
        --offline-access \
        --no-input
    ```

5. Open the `.env` file in your project directory. Add the following lines, replacing the values with the ones you noted in the previous steps:

    ```ini
    # The Auth0 domain for your tenant (e.g. tenant.region.auth0.com):
    AUTH0_DOMAIN=...

    # The application `client_id` you noted above:
    AUTH0_CLIENT_ID=...

    # The application `client_secret` you noted above:
    AUTH0_CLIENT_SECRET=...

    # The API `identifier` you used above:
    AUTH0_AUDIENCE=...
    ```

    Additional variables are available for configuration. More on those in the [configuration guide](./docs/Configuration.md).

</details>

## Quickstarts

-   [Session-based Authentication](https://auth0.com/docs/quickstart/webapp/laravel) ([GitHub](https://github.com/auth0-samples/laravel))
-   [Token-based Authorization](https://auth0.com/docs/quickstart/backend/laravel) ([GitHub](https://github.com/auth0-samples/laravel))

## Examples

<details>
<summary><b>Authentication</b></summary>
 

The SDK automatically registers all the necessary authentication services within the `web` middleware group for your application. Once you have [configured the SDK](#configuration) your users will be able to authenticate with your application using Auth0.

The SDK automatically registers the following routes to facilitate authentication:

| Route       | Purpose                            |
| ----------- | ---------------------------------- |
| `/login`    | Initiates the authentication flow. |
| `/logout`   | Logs the user out.                 |
| `/callback` | Handles the callback from Auth0.   |

> **Note**  
> See [the configuration guide](./docs/Configuration.md) for information on customizing this behavior.

---

</details>

<details>
<summary><b>Access Control</b></summary>
 

The SDK automatically registers its authentication and authorization guards within the `web` and `api` middleware groups for your Laravel application.

To require a user to be authenticated to access a route, use Laravel's `auth` middleware:

```php
Route::get('/private', function () {
  return response('Welcome! You are logged in.');
})->middleware('auth');
```

You can also require that the user have specific permissions to access a route, using Laravel's `can` middleware:

```php
Route::get('/scope', function () {
    return response('You have the `read:messages` permissions, and can therefore access this resource.');
})->middleware('auth')->can('read:messages');
```

> **Note**  
> Permissions require that [RBAC](https://auth0.com/docs/manage-users/access-control/rbac) be enabled within [your API settings](https://manage.auth0.com/#/apis).

---

</details>

<details>
<summary><b>Users and Tokens</b></summary>
 

Laravel's `Auth` Facade (or the `auth()` global helper) can be used to retrieve information about the authenticated user, or the access token used to authorize the request.

For example, for routes using the `web` middleware group in `routes/web.php`:

```php
Route::get('/', function () {
  if (! auth()->check()) {
    return response('You are not logged in.');
  }

  $user = auth()->user();
  $name = $user->name ?? 'User';
  $email = $user->email ?? '';

  return response("Hello {$name}! Your email address is {$email}.");
});
```

Alternatively, for routes using the `api` middleware group in `routes/api.php`:

```php
Route::get('/', function () {
  if (! auth()->check()) {
    return response()->json([
      'message' => 'You did not provide a token.',
    ]);
  }

  return response()->json([
    'message' => 'Your token is valid; you are authorized.',
    'id' => auth()->id(),
    'token' => auth()?->user()?->getAttributes(),
  ]);
});
```

---

</details>

<details>
<summary><b>Management API</b></summary>
 

You can issue [Auth0 Management API](https://auth0.com/docs/api/management/v2) calls through the SDK's `management()` method.

> **Note**  
> Before your application can make calls to the Management API, you must [enable your application to communicate with the Management API](./docs/Management.md#management-api-permissions) or configure the SDK with [a management token](./docs/Configuration.md#management-token).

For example, you can update a user's metadata by calling the `management()->users()->update()` method:

```php
use Auth0\Laravel\Facade\Auth0;

Route::get('/colors', function () {
  $colors = ['red', 'blue', 'green', 'black', 'white', 'yellow', 'purple', 'orange', 'pink', 'brown'];

  // Update the authenticated user with a randomly assigned favorite color.
  Auth0::management()->users()->update(
    id: auth()->id(),
    body: [
        'user_metadata' => [
            'color' => $colors[random_int(0, count($colors) - 1)]
        ]
    ]
  );

  // Retrieve the user's updated profile.
  $profile = Auth0::management()->users()->get(auth()->id());

  // For interoperability, the SDK returns all API responses as
  // PSR-7 Responses that contain the JSON response.

  // You can use the `json()` helper to unpack the PSR-7, and
  // convert the API's JSON response to a native PHP array.
  $profile = Auth0::json($profile);

  // Read the user's profile.
  $color = $profile['user_metadata']['color'] ?? 'unknown';
  $name = auth()->user()->name;

  return response("Hello {$name}! Your favorite color is {$color}.");
})->middleware('auth');
```

All the SDK's Management API methods are [documented here](./docs/Management.md).

</details>

## Documentation

-   [Installation](./docs/Installation.md) — Installing the SDK and generating configuration files.
-   [Configuration](./docs/Configuration.md) — Configuring the SDK using JSON files or environment variables.
-   [Management](./docs/Management.md) — Using the SDK to call the [Management API](https://auth0.com/docs/api/management/v2).
-   [Users](./docs/Users.md) — Extending the SDK to support persistent storage and [Eloquent](https://laravel.com/docs/eloquent).
-   [Events](./docs/Events.md) — Hooking into SDK [events](https://laravel.com/docs/events) to respond to specific actions.
-   [Octane](./docs/Octane.md) — We do not support using the SDK with [Octane](https://laravel.com/docs/octane) at this time.

You may also find the following resources helpful:

-   [Auth0 Documentation Hub](https://www.auth0.com/docs)
-   [Auth0 Management API Explorer](https://auth0.com/docs/api/management/v2)
-   [Auth0 Authentication API Explorer](https://auth0.com/docs/api/authentication)

Contributions to improve our documentation [are welcomed](https://github.com/auth0/laravel-auth0/pull).

## Community

The [Auth0 Community](https://community.auth0.com) is where you can ask questions, get support, and share your projects.

## Contributing

We appreciate feedback and contributions to this library. Before you get started, please review Auth0's [General Contribution guidelines](https://github.com/auth0/open-source-template/blob/master/GENERAL-CONTRIBUTING.md). Read [our contributing guide](./CONTRIBUTING.md) to learn about how to propose bug fixes and improvements, and how to build and test your changes to the library.

To provide feedback or report a bug, [please raise an issue](https://github.com/auth0/laravel-auth0/issues).

## Code of Conduct

Participants are expected to adhere to our [code of conduct](https://github.com/auth0/open-source-template/blob/master/CODE-OF-CONDUCT.md) when interacting with this project.

## Security

If you believe you have found a security vulnerability, we encourage you to responsibly disclose this and not open a public issue. We will investigate all reports. The [Responsible Disclosure Program](https://auth0.com/whitehat) details the procedure for disclosing security issues.

## License

This library is open-sourced software licensed under the [MIT license](./LICENSE.md).

---

<p align="center">
  <picture>
    <source media="(prefers-color-scheme: light)" srcset="https://cdn.auth0.com/website/sdks/logos/auth0_light_mode.png" width="150">
    <source media="(prefers-color-scheme: dark)" srcset="https://cdn.auth0.com/website/sdks/logos/auth0_dark_mode.png" width="150">
    <img alt="Auth0 Logo" src="https://cdn.auth0.com/website/sdks/logos/auth0_light_mode.png" width="150">
  </picture>
</p>

<p align="center">Auth0 is an easy-to-implement, adaptable authentication and authorization platform.<br />To learn more, check out <a href="https://auth0.com/why-auth0">"Why Auth0?"</a></p>
