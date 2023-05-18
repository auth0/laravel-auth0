# Changelog

## [Unreleased]

### Added

-   This release adds support for authenticating using **[Pushed Authorization Requests](https://www.rfc-editor.org/rfc/rfc6749)**.

-   This release introduces **two new Authentication Guards** which provide a streamlined integration experience for developers that need to simultaneously support both session-based authentication and token-based endpoint authorization in their Laravel applications.

    | Guard                 | Class                                           | Description                   |
    | --------------------- | ----------------------------------------------- | ----------------------------- |
    | `auth0.authenticator` | `Auth0\Laravel\Auth\Guards\AuthenticationGuard` | Session-based authentication. |
    | `auth0.authorizer`    | `Auth0\Laravel\Auth\Guards\AuthorizationGuard`  | Token-based authorization.    |

-   These guards are compatible with Laravel's Authentication API and support the standard `auth` middleware.

-   These guards are compatible with Laravel's Authorization API and support the standard `can` middleware, and the `Guard` facade, and work with the Policies API.

-   3 new pre-built Guards are available: `scope` and `permission`, as well as a dynamic `*:*`. This enables you to verify whether the user's access token has a particular scope or (if RBAC is enabled on the Auth0 API) a particular permission. For example `Gate::check('scope', 'email')` or `Route::get(/*...*/)->can('read:messages')`.

-   The SDK now automatically registers these guards to Laravel's standard `web` and `api` middleware groups, respectively. Manual Guard setup in `config/auth.php` is no longer necessary.

-   The SDK now automatically registers the Authentication routes. Manual route setup in `routes/web.php` is no longer necessary.

-   2 new routing Middleware have been added: `Auth0\Laravel\Http\Middleware\AuthenticatorMiddleware` and `Auth0\Laravel\Http\Middleware\AuthorizerMiddleware`. These are automatically registered with your Laravel application, and ensure the Auth0 Guards are used for authentication for `web` routes and authorization for `api` routes, respectively. This replaces the need for the `guard` middleware or otherwise manual Guard assignment in your routes.

### Improved

-   We've introduced **a new configuration syntax**. This new syntax is more flexible and allows for more complex configuration scenarios, and introduces support for multiple guard instances. Developers using the previous syntax will have their existing configurations applied to all guards uniformly.

-   The SDK can now **configure itself using a `.auth0.json` file in the project root directory**. This file can be generated [using the Auth0 CLI](./docs/JSON%20Configuration.md), and provides a significantly simpler configuration experience for developers.

-   The previous `auth0.guard` Guard (`Auth0\Laravel\Auth\Guard`) has been **refactored** as a lightweight wrapper around the new `AuthenticationGuard` and `AuthorizationGuard` guards.

## [7.7.0](https://github.com/auth0/laravel-auth0/tree/7.7.0) (2023-04-26)

### Added

-   `Auth0\Laravel\Auth0` now has a `management()` shortcut method for issuing Management API calls. ([\#376](https://github.com/auth0/laravel-auth0/pull/376))

-   `Auth0\Laravel\Auth0\Guard` now has a `refreshUser()` method for querying `/userinfo` endpoint and refreshing the authenticated user's cached profile data. ([\#375](https://github.com/auth0/laravel-auth0/pull/375))

-   `Auth0\Laravel\Http\Controller\Stateful\Login` now raises a `LoginAttempting` event, offering an opportunity to customize the authorization parameters before the login redirect is issued. ([\#382](https://github.com/auth0/laravel-auth0/pull/382))

### Improved

-   The `tokenCache`, `managementTokenCache`, `sessionStorage` and `transientStorage` configuration values now support `false` or `string` values pointing to class names (e.g. `\Some\Cache::class`) or class aliases (e.g. `cache.psr6`) registered with Laravel. ([\#381](https://github.com/auth0/laravel-auth0/pull/381))

## [7.6.0](https://github.com/auth0/laravel-auth0/tree/7.6.0) (2023-04-12)

### Added

-   `Auth0\Laravel\Http\Middleware\Guard`, new middleware that forces Laravel to route requests through a group using a specific Guard. ([\#362](https://github.com/auth0/laravel-auth0/pull/362))

### Improved

-   `Auth0\Laravel\Http\Middleware\Stateful\Authenticate` now remembers the intended route (using `redirect()->setIntendedUrl()`) before kicking off authentication flow redirect. Users will be returned to the memorized intended route after completing their authentication flow. ([\#364](https://github.com/auth0/laravel-auth0/pull/364))

### Fixed

-   legacyGuardUserMethod behavior should use `$session`, not `$token` ([\#353](https://github.com/auth0/laravel-auth0/pull/365))

## [7.5.2](https://github.com/auth0/laravel-auth0/tree/7.5.2) (2023-04-10)

### Fixed

-   Relaxed response types from middleware to use low-level `Symfony\Component\HttpFoundation\Response` class, allowing for broader and custom response types.

## [7.5.1](https://github.com/auth0/laravel-auth0/tree/7.5.1) (2023-04-04)

### Fixed

-   Resolved an issue wherein custom user repositories could fail to be instantiated under certain circumstances.

## [7.5.0](https://github.com/auth0/laravel-auth0/tree/7.5.0) (2023-04-03)

This release includes support for Laravel 10, and major improvements to the internal state handling mechanisms of the SDK.

### Added

-   Support for Laravel 10 [#349](https://github.com/auth0/laravel-auth0/pull/349)
-   New `Auth0\Laravel\Traits\Imposter` trait to allow for easier testing. [Example usage](./tests/Unit/Traits/ImpersonateTest.php)
-   New Exception types have been added for more precise error catching.

### Improved

The following changes have no effect on the external API of this package, but may affect internal usage.

-   `Guard` will now more reliably detect changes in the underlying Auth0-PHP SDK session state.
-   `Guard` will now more reliably sync changes back to the underlying Auth0-PHP SDK session state.
-   `StateInstance` concept has been replaced by new `Credentials` entity.
-   `Guard` updated to use new `Credentials` entity as primary internal storage for user data.
-   `Auth0\Laravel\Traits\ActingAsAuth0User` was updated to use new`Credentials` entity.
-   The HTTP middleware have been refactored to more clearly differentiate between token and session based identities.
-   The `authenticate`, `authenticate.optional` and `authorize.optional` HTTP middleware now support scope filtering, as `authorize` already did.

-   Upgraded test suite to use PEST 2.0 framework.
-   Updated test coverage to 100%.

### Fixed

-   A 'Session store not set on request' error could occur when downstream applications implemented unit testing that use the Guard. This should be resolved now.
-   `Guard` would not always honor the `provider` configuration value in `config/auth.php`.
-   `Guard` is no longer defined as a Singleton to better support applications that need multi-guard configurations.

### Notes

#### Changes to `user()` behavior

This release includes a significant behavior change around the `user()` method of the Guard. Previously, by simply invoking the method, the SDK would search for any available credential (access token, device session, etc.) and automatically assign the user within the Guard. The HTTP middleware have been upgraded to handle the user assignment step, and `user()` now only returns the current state of user assignment without altering it.

A new property has been added to the `config/auth0.php` configuration file: `behavior`. This is an array. At this time, there is a single option: `legacyGuardUserMethod`, a bool. If this value is set to true, or if the key is missing, the previously expected behavior will be applied, and `user()` will behave as it did before this release. The property defaults to `false`.

#### Changes to Guard and Provider driver aliases

We identified an issue with using identical alias naming for both the Guard and Provider singletons under Laravel 10, which has required us to rename these aliases. As previous guidance had been to instantiate these using their class names, this should not be a breaking change in most cases. However, if you had used `auth0` as the name for either the Guard or the Provider drivers, kindly note that these have changed. Please use `auth0.guard` for the Guard driver, and `auth0.provider` for the Provider driver. This is a regrettable change, but was necessary for adequate Laravel 10 support.

## [7.4.0](https://github.com/auth0/laravel-auth0/tree/7.4.0) (2022-12-12)

### Added

-   feat: Add `Auth0\Laravel\Event\Middleware\...` event hooks [\#340](https://github.com/auth0/laravel-auth0/pull/340)
-   feat: Add `Auth0\Laravel\Event\Configuration\Building` event hook [\#339](https://github.com/auth0/laravel-auth0/pull/339)

## [7.3.0](https://github.com/auth0/laravel-auth0/tree/7.3.0) (2022-11-07)

### Added

-   add: Raise additional Laravel Auth Events [\#331](https://github.com/auth0/laravel-auth0/pull/331)

### Fixed

-   fix: `env()` incorrectly assigns `cookieExpires` to a `string` value [\#332](https://github.com/auth0/laravel-auth0/pull/332)
-   fix: Auth0\Laravel\Cache\LaravelCachePool::createItem returning a cache miss [\#329](https://github.com/auth0/laravel-auth0/pull/329)

## [7.2.2](https://github.com/auth0/laravel-auth0/tree/7.2.2) (2022-10-19)

### Fixed

-   Restore `php artisan vendor:publish` command [\#321](https://github.com/auth0/laravel-auth0/pull/321)
-   Bump minimum `auth0/auth0-php` version to `^8.3.4` [\#322](https://github.com/auth0/laravel-auth0/pull/322)

## [7.2.1](https://github.com/auth0/laravel-auth0/tree/7.2.1) (2022-10-13)

### Fixed

-   `Auth0\Laravel\Auth0` no longer requires a session configuration for stateless strategies, restoring previous behavior. [\#317](https://github.com/auth0/laravel-auth0/pull/317)
-   The SDK now requires `^3.0` of the `psr/cache` dependency, to accomodate breaking changes made in the upstream interface (typed parameters and return types) for PHP 8.0+. [\#316](https://github.com/auth0/laravel-auth0/pull/316)

## [7.2.0](https://github.com/auth0/laravel-auth0/tree/7.2.0) (2022-10-10)

### Improved

-   `Auth0\Laravel\Store\LaravelSession` has been added as the default `sessionStorage` and `transientStorage` interfaces for the underlying [Auth0-PHP SDK](https://github.com/auth0/auth0-PHP/). The SDK now leverages the native [Laravel Session APIs](https://laravel.com/docs/9.x/session) by default. [\#307](https://github.com/auth0/laravel-auth0/pull/307)¹
-   `Auth0\Laravel\Cache\LaravelCachePool` and `Auth0\Laravel\Cache\LaravelCacheItem` have been added as the default `tokenCache` and `managementTokenCache` interfaces for the underlying [Auth0-PHP SDK](https://github.com/auth0/auth0-PHP/). The SDK now leverages the native [Laravel Cache APIs](https://laravel.com/docs/9.x/cache) by default. [\#307](https://github.com/auth0/laravel-auth0/pull/307)
-   `Auth0\Laravel\Auth\Guard` now supports the `viaRemember` method. [\#306](https://github.com/auth0/laravel-auth0/pull/306)
-   `Auth0\Laravel\Http\Middleware\Stateless\Authorize` now returns a 401 status instead of 403 for unauthenticated users. [\#304](https://github.com/auth0/laravel-auth0/issues/304)
-   PHP 8.0 is now the minimum supported runtime version. Please review the [README](README.md) for more information on support windows.

¹ This change may require your application's users to re-authenticate. You can avoid this by changing the `sessionStorage` and `transientStorage` options in your SDK configuration to their previous default instances of `Auth0\SDK\Store\CookieStore`, but it is recommended you migrate to the new `LaravelSession` default.

## [7.1.0](https://github.com/auth0/laravel-auth0/tree/7.1.0) (2022-08-08)

### Improved

-   Return interfaces instead of concrete classes [\#296](https://github.com/auth0/laravel-auth0/pull/296)
-   change: Use class names for app() calls [\#291](https://github.com/auth0/laravel-auth0/pull/291)

### Fixed

-   Fix: `Missing Code` error on Callback Route for Octane Customers [\#297](https://github.com/auth0/laravel-auth0/pull/297)

## [7.0.1](https://github.com/auth0/laravel-auth0/tree/7.0.1) (2022-06-01)

### Fixed

-   Fixed an issue in `Auth0\Laravel\Http\Controller\Stateful\Callback` where `$errorDescription`'s value was assigned an incorrect value when an error was encountered. [\#266](https://github.com/auth0/laravel-auth0/pull/288)

## [7.0.0](https://github.com/auth0/laravel-auth0/tree/7.0.0) (2022-03-21)

Auth0 Laravel SDK v7 includes many significant changes over previous versions:

-   Support for Laravel 9.
-   Support for Auth0-PHP SDK 8.
-   New authentication route controllers for plug-and-play login support.
-   Improved authentication middleware for regular web applications.
-   New authorization middleware for token-based backend API applications.

As expected with a major release, Auth0 Laravel SDK v7 includes breaking changes. Please review the [upgrade guide](UPGRADE.md) thoroughly to understand the changes required to migrate your application to v7.

### Breaking Changes

-   Namespace has been updated from `Auth0\Login` to `Auth0\Laravel`
-   Auth0-PHP SDK dependency updated to V8
-   New configuration format
-   SDK now self-registers its services and middleware
-   New UserProvider API

> Changelog entries for releases prior to 8.0 have been relocated to [CHANGELOG.ARCHIVE.md](CHANGELOG.ARCHIVE.md).
