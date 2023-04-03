# Auth0 Laravel SDK Examples Guide

This guide will walk you through the process of building and configuring the example applications provided for this SDK.

-   [Requirements](#requirements)
-   [Setup](#setup)
-   [Creating the Examples](#creating-the-examples)
-   [Personalized Documentation](#personalized-documentation)
-   [Running the Examples](#running-the-examples)
-   [Starting Fresh](#starting-fresh)
-   [Troubleshooting](#troubleshooting)

## Requirements

-   PHP 8.1 (`php -v`)
-   An Auth0 account, which you can [create for free](https://auth0.com/signup)

The script will download local copies of [the Composer package manager](https://getcomposer.org/) and [the Auth0 CLI](https://github.com/auth0/auth0-cli), so you don't need to have those installed on your machine beforehand.

## Setup

Clone [the repository](https://github.com/auth0/laravel-auth0), open a shell to the `examples` root directory, and run the following command:

```bash
./examples setup
```

Follow the prompts to complete the setup process.

## Creating the Examples

With a shell open to the `examples` root directory, run the following command:

```bash
./examples create
```

Follow the prompts to complete the setup process.

This script creates two directories, `laravel-9` and `laravel-10`, each representing the supported major versions of the framework.

Within each of those framework directories, the script will also create various examples of application types you might want to build using the SDK.

-   `web` is a traditional web application that uses sessions, and performs common interactions like logging in and reading a user's profile.
-   `web.octane` is a variation of the `web` application that uses [Laravel Octane](https://laravel.com/docs/octane).
-   `api` is a stateless backend API application that authorizes endpoints using access tokens.

The script will handle the installation of the SDK in these example applications, and all examples will be configured to run from `http://localhost:8000`.

## Personalized Documentation

As part of the creation process, the script will generate a series of personalized documentation files, already configured with the access token and application details that were setup for you. You can find in the individual directories of each example, each beginning with `EXAMPLES`.

## Running the Examples

With a shell open to the `examples` root directory, run the following command:

```bash
./examples run laravel-9 web
```

Update the `laravel-9` and `web` values to match the framework and example you'd like to run, respectively.

## Starting Fresh

With a shell open to the `examples` root directory, run the following command:

```bash
./examples reset
```

This will delete all the local example files, as well as the Auth0 Application and API created during the setup process.

You will remain logged in to the Auth0 CLI, but you can log out with the following command:

```bash
./auth0 logout
```

## Troubleshooting

If you encounter any issues with the examples, please run the following open to the `examples` root directory, run the following command:

```bash
./examples check
```

This will run a series of diagnostic checks to help identify common issues.

If you continue to have problems, please open an issue.
