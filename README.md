# Laravel Auth0 Plugin

This plugin helps you integrate your [Laravel](https://laravel.com/) WebApp with [Auth0](https://auth0.com/) to achieve Single Sign On with a few simple steps.

- Master targets compatibility with Laravel 5.7 and above.
- The 3.x branch (not maintained) targets Laravel 5.2 compatibility.
- The 2.x branch (not maintained) targets Laravel 5.0 and 5.1 compatibility.
- If you are working with an older version (Laravel 4.x), use version 1.0.* (not maintained)

[![CircleCI](https://img.shields.io/circleci/project/github/auth0/laravel-auth0/master.svg)](https://circleci.com/gh/auth0/laravel-auth0)
[![Latest Stable Version](https://poser.pugx.org/auth0/login/v/stable)](https://packagist.org/packages/auth0/login)
[![License](https://poser.pugx.org/auth0/login/license)](https://packagist.org/packages/auth0/login)
[![Total Downloads](https://poser.pugx.org/auth0/login/downloads)](https://packagist.org/packages/auth0/login)

## Documentation

Please see the [Laravel webapp quickstart](https://auth0.com/docs/quickstart/webapp/laravel) for a complete guide on how to install this in an existing project or to download a pre-configured sample project. Additional documentation on specific scenarios is below.

### Setting up a JWKs cache

In the `register` method of your `AppServiceProvider` add:

```php
// app/Providers/AppServiceProvider.php
use Illuminate\Support\Facades\Cache;
// ...
    public function register()
    {
        // ...
        $this->app->bind(
            '\Auth0\SDK\Helpers\Cache\CacheHandler',
            function() {
                static $cacheWrapper = null;
                if ($cacheWrapper === null) {
                $cache = Cache::store();
                $cacheWrapper = new LaravelCacheWrapper($cache);
            }
            return $cacheWrapper;
        });
    }
```

You can implement your own cache strategy by creating a new class that implements the `Auth0\SDK\Helpers\Cache\CacheHandler` contract, or just use the cache strategy you want by picking that store with `Cache::store('your_store_name')`;

### Storing users in your database

You can customize the way you handle the users in your application by creating your own `UserRepository`. This class should implement the `Auth0\Login\Contract\Auth0UserRepository` contract. Please see the [Custom User Handling section of the Laravel Quickstart](https://auth0.com/docs/quickstart/webapp/laravel#optional-custom-user-handling) for the latest example.

## Installation

Install this plugin into a new or existing project using [Composer](https://getcomposer.org/doc/00-intro.md):

```bash
$ composer require auth0/login:"~5.0"
```

Additional steps to install can be found in the [quickstart](https://auth0.com/docs/quickstart/webapp/laravel#integrate-auth0-in-your-application).

## Contributing

We appreciate feedback and contribution to this repo! Before you get started, please see the following:

- [Auth0's Contribution guidelines](https://github.com/auth0/.github/blob/master/CONTRIBUTING.md)
- [Auth0's Code of Conduct](https://github.com/auth0/open-source-template/blob/master/CODE-OF-CONDUCT.md)

## Support + Feedback

Include information on how to get support. Consider adding:

- Use [Community](https://community.auth0.com/tags/laravel) for usage, questions, specific cases
- Use [Issues](https://github.com/auth0/laravel-auth0/issues) for code-level support

## What is Auth0?

Auth0 helps you to easily:

- implement authentication with multiple identity providers, including social (e.g., Google, Facebook, Microsoft, LinkedIn, GitHub, Twitter, etc), or enterprise (e.g., Windows Azure AD, Google Apps, Active Directory, ADFS, SAML, etc.)
- log in users with username/password databases, passwordless, or multi-factor authentication
- link multiple user accounts together
- generate signed JSON Web Tokens to authorize your API calls and flow the user identity securely
- access demographics and analytics detailing how, when, and where users are logging in
- enrich user profiles from other data sources using customizable JavaScript rules

[Why Auth0?](https://auth0.com/why-auth0)

## License

The Auth0 Laravel Login plugin is licensed under MIT - [LICENSE](LICENSE.txt)
