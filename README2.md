![laravel-auth0](https://cdn.auth0.com/website/sdks/banners/laravel-auth0-banner.png)

<p align="right">
<a href="https://github.com/auth0/laravel-auth0/actions"><img src="https://github.com/auth0/laravel-auth0/actions/workflows/main.yml/badge.svg?event=push" alt="Build Status"></a>
<a href="https://codecov.io/gh/auth0/laravel-auth0"><img src="https://codecov.io/gh/auth0/laravel-auth0/branch/main/graph/badge.svg?token=vEwn6TPADf" alt="Code Coverage"></a>
<a href="https://packagist.org/packages/auth0/laravel-auth0"><img src="https://img.shields.io/packagist/dt/auth0/login" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/auth0/login"><img src="https://img.shields.io/packagist/l/auth0/login" alt="License"></a>
</p>

## Requirements

Your application must use a [supported Laravel version](https://laravelversions.com/en), and your environment must be running a [supported PHP version](https://www.php.net/supported-versions.php). We do not support versions of Laravel or PHP that are no longer supported by their maintainers.

| SDK  | Laravel | PHP  | Supported Until |
| ---- | ------- | ---- | --------------- |
| 7.5+ | 10      | 8.2+ | Feb 2025        |
|      |         | 8.1+ | Nov 2024        |
| 7.0+ | 9       | 8.2+ | Feb 2024        |
|      |         | 8.1+ | Feb 2024        |
|      |         | 8.0+ | Nov 2023        |

You will also need [Composer](https://getcomposer.org/) and an [Auth0 account](https://auth0.com/signup).

The [Auth0 CLI](https://auth0.com/docs/cli) is recommended for configuring the SDK, but not required.

### Installation

<details>

<summary>Using a Quickstart</summary>

We provide a bootstrapped Laravel application pre-configured with the SDK that you can use to get started quickly.

```shell
composer create-project auth0-samples/laravel auth0-laravel-app
```
</details>

<details>
<summary>Using Composer</summary>

<p style="margin-top: 1rem">Run the following command in your project directory to install the SDK:</p>

```shell
composer require auth0/login:^7.8 --update-with-all-dependencies
```

Then generate an SDK configuration file for your application:

```shell
php artisan vendor:publish --tag auth0
```
</details>

### Configuration

<details>
<summary>Using the Auth0 CLI (Recommended)</summary>

You will need to download the CLI and authenticate it with your Auth0 account. You can then use it to create the configuration files for your application.

1. Download the [Auth0 CLI](https://github.com/auth0/auth0-cli) to your application's root directory:

    > **Note**
    > If you are using the Quickstart application, the CLI was bundled for you, and you can skip to the next step.

    ```shell
    curl -sSfL https://raw.githubusercontent.com/auth0/auth0-cli/main/install.sh | sh -s -- -b .
    ```

2. Then authenticate with your Auth0 account:

    ```shell
    ./auth0 login
    ```

3. Create a new application with Auth0:

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

4. Create a new API with Auth0

    ```shell
    ./auth0 apis create \
    --name "My Laravel Application API" \
    --identifier "https://github.com/auth0/laravel-auth0" \
    --offline-access \
    --no-input \
    --json > .auth0.api.json
    ```

5. The files created by these commands contain sensitive credentials. It is important you do not commit these to version control.

    If you're using Git, you should add them to your `.gitignore` file:

    ```bash
    echo ".auth0.*.json" >> .gitignore
    ```
</details>

<details>
<summary>Using Environment Variables</summary>

</details>

## Documentation

The documentation is divided into several sections:

-   [Getting Started](./README.md#getting-started) — Installing and configuring the SDK.
-   [Examples](./EXAMPLES.md) — Answers and solutions for common questions and scenarios.
-   Reference:
    -   [Installation](./docs/Installation.md) — Installing the SDK and generating configuration files.
    -   [Configuration](./docs/Configuration.md) — Configuring the SDK using JSON files or environment variables.
    -   [Management](./docs/Management.md) — Using the SDK to call the [Management API](https://auth0.com/docs/api/management/v2).
    -   [Users](./docs/Users.md) — Extending the SDK to support persistent storage and [Eloquent](https://laravel.com/docs/eloquent).
    -   [Events](./docs/Events.md) — Hooking into SDK [events](https://laravel.com/docs/events) to respond to specific actions.
-   [Auth0 Documentation](https://www.auth0.com/docs)
-   [Auth0 Management API Explorer](https://auth0.com/docs/api/management/v2)
-   [Auth0 Authentication API Explorer](https://auth0.com/docs/api/authentication)

You can improve it by sending pull requests to [this repository](https://github.com/auth0/laravel-auth0).

## Examples

We have several examples on the website. Here is the first one to get you started:

```php

```

## Community

The main purpose of this repository is to continue evolving React core, making it faster and easier to use. Development of React happens in the open on GitHub, and we are grateful to the community for contributing bugfixes and improvements. Read below to learn how you can take part in improving React.

## Contributing

## Code of Conduct

## Security

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
