# Installation

- [Prerequisites](#prerequisites)
- [Install the SDK](#install-the-sdk)
  - [Using Quickstart (Recommended)](#using-quickstart-recommended)
  - [Installation with Composer](#installation-with-composer)
    - [Create a Laravel Application](#create-a-laravel-application)
    - [Install the SDK](#install-the-sdk-1)
- [Install the CLI](#install-the-cli)
    - [Authenticate the CLI](#authenticate-the-cli)
- [Configure the SDK](#configure-the-sdk)
  - [Using JSON (Recommended)](#using-json-recommended)
  - [Using Environment Variables](#using-environment-variables)

## Prerequisites

To integrate our SDK, your application must use a [supported Laravel version](https://laravelversions.com/en), and your environment must run a [supported PHP version](https://www.php.net/supported-versions.php). We do not support versions of either that are no longer supported by their maintainers. Please review [our support policy](./Support.md) for more information.

| SDK  | Laravel | PHP  | Supported Until |
| ---- | ------- | ---- | --------------- |
| 7.5+ | 10      | 8.2+ | Feb 2025        |
|      |         | 8.1+ | Nov 2024        |
| 7.0+ | 9       | 8.2+ | Feb 2024        |
|      |         | 8.1+ | Feb 2024        |
|      |         | 8.0+ | Nov 2023        |

You will also need [Composer 2.0+](https://getcomposer.org/) and an [Auth0 account](https://auth0.com/signup).

## Install the SDK

Ensure that your development environment has [supported versions](#prerequisites) of PHP and [Composer](https://getcomposer.org/) installed. If you're using macOS, PHP and Composer can be installed via [Homebrew](https://brew.sh/). It's also advisable to [install Node and NPM](https://nodejs.org/).

### Using Quickstart (Recommended)

- Create a new Laravel 9 project pre-configured with the SDK:

    ```shell
    composer create-project auth0-samples/laravel auth0-laravel-app
    ```

### Installation with Composer

#### Create a Laravel Application

- If you do not already have one, you can Create a new Laravel 9 application with the following command:

    ```shell
    composer create-project laravel/laravel:^9.0 auth0-laravel-app
    ```

#### Install the SDK

1. Run the following command from your project directory to install the SDK:

    ```shell
    composer require auth0/login:^7.8 --update-with-all-dependencies
    ```

2. Generate an SDK configuration file for your application:

    ```shell
    php artisan vendor:publish --tag auth0
    ```

## Install the CLI

Install the [Auth0 CLI](https://github.com/auth0/auth0-cli) to create and manage Auth0 resources from the command line.

- macOS with [Homebrew](https://brew.sh/):

    ```shell
    brew tap auth0/auth0-cli && brew install auth0
    ```

- Linux or macOS:

    ```shell
    curl -sSfL https://raw.githubusercontent.com/auth0/auth0-cli/main/install.sh | sh -s -- -b .
    sudo mv ./auth0 /usr/local/bin
    ```

- Windows with [Scoop](https://scoop.sh/):

    ```cmd
    scoop bucket add auth0 https://github.com/auth0/scoop-auth0-cli.git
    scoop install auth0
    ```

### Authenticate the CLI

- Authenticate the CLI with your Auth0 account. Choose "as a user," and follow the prompts.

    ```shell
    auth0 login
    ```

## Configure the SDK

### Using JSON (Recommended)

1. Register a new application with Auth0:

    ```shell
    auth0 apps create \
      --name "My Laravel Application" \
      --type "regular" \
      --auth-method "post" \
      --callbacks "http://localhost:8000/callback" \
      --logout-urls "http://localhost:8000" \
      --reveal-secrets \
      --no-input \
      --json > .auth0.app.json
    ```

2. Register a new API with Auth0:

    ```shell
    auth0 apis create \
      --name "My Laravel Application API" \
      --identifier "https://github.com/auth0/laravel-auth0" \
      --offline-access \
      --no-input \
      --json > .auth0.api.json
    ```

3. Add the new files to `.gitignore`:

    Linux and macOS:

    ```bash
    echo ".auth0.*.json" >> .gitignore
    ```

    Windows PowerShell:

    ```powershell
    Add-Content .gitignore "`n.auth0.*.json"
    ```

    Windows Command Prompt:

    ```cmd
    echo .auth0.*.json >> .gitignore
    ```

### Using Environment Variables

1. Register a new application with Auth0:

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

    Make a note of the `client_id` and `client_secret` values in the output.

2. Register a new API with Auth0:

    ```shell
    auth0 apis create \
      --name "My Laravel Application API" \
      --identifier "https://github.com/auth0/laravel-auth0" \
      --offline-access \
      --no-input
    ```

3. Open the `.env` file found inside your project directory, and add the following lines, replacing the values with the ones you noted in the previous steps:

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

    Additional configuration environment variables can be found in the [configuration guide](./Configuration.md#environment-variables).
