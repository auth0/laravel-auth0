# Upgrade Guide

## v7 Migration Guide

Auth0 Laravel SDK v7 includes many significant changes over previous versions:

- Support for Laravel 9.
- Support for Auth0-PHP SDK 8.
- New authentication route controllers for plug-and-play login support.
- Improved authentication middleware for regular web applications.
- New authorization middleware for token-based backend API applications.

As expected with a major release, Auth0 Laravel SDK v7 includes breaking changes. Please review this guide thoroughly to undrstand the changes required to migrate your application to v7.

---

### Before you begin: Updated Requirements

- Laravel 8 and Laravel 9 are supported by the Auth0 Laravel SDK v7 release.
- PHP ≥7.4 is supported by the SDK when paired with Laravel 8.
- PHP ≥8.0 is supported by the SDK when paired with Laravel 9.¹

¹ This is a requirement of Laravel itself; only PHP 8+ will be supported going forward.

---

### Breaking Changes Summary

- Namespace has been updated from `Auth0\Login` to `Auth0\Laravel`.
- The Auth0-PHP SDK dependency has been updated from V7 to V8, which [may introduce breaking API changes](https://github.com/auth0/auth0-PHP/blob/main/UPGRADE.md) that will require further changes in your app outside the scope of this Laravel SDK.
- A simplified configuration file format is present. You will need to regenerate your config file. (Instructions below.)
- Changes to application files are no longer necessary, as the SDK registers services and middleware itself. You should remove any `config/app.php` or `app/HttpKernel.php` customizations made to avoid conflicts. (Instructions below.)

---

### Migration Guidance

#### Update Configuration Scheme

- Configuration filename is now `config/auth0.php`.
- Configuration format has been updated to support Auth0-PHP SDK 8.

1. Delete any previous laravel-auth0 configuration files present in your application.
2. Use `php artisan vendor:publish --tag=auth0-config` to generate an updated config file.
3. Review new configuration instructions in the [README](README.md#configuration-the-sdk).

#### Remove `config\app.php` modifications

- Previously, the SDK required you to add service provider classes to the `providers` array in this file.
- This is no longer necessary, as the SDK now registers services itself.

1. Remove any references to the SDK in your `providers` array.

#### Remove `app\Http\Kernel.php` modifications

- Previously, the SDK required you to add middleware classes to the middleware arrays in this file.
- This is no longer necessary, as the SDK now registers these itself.

1. Remove any references to the SDK in your `middleware` arrays.
2. Update any router middleware references in your app to the types instructed in the [README](README.md#protecting-routes-with-middleware).

#### Update to new authentication routes, as appropriate
Note: This only applies to regular web application types.

- Previously, the SDK required you to write boilerplate around login, logout and callback routes.
- The SDK now provides plug-and-play middleware that handles authentication flows, appropriate for most application needs.

1. Remove any route logic around login, logout or callback routes.
2. Implement the new authentication utility routes as instructed in the [README](README.md#authentication-routes).

#### Update to new `auth0.authenticate` middleware, as appropriate
Note: This only applies to regular web application types.

- Previously, the SDK advised you to register the Auth0 authentication middleware yourself in the `app\Http\Kernel.php`, which invited you to specify custom naming schemes for these middlewares.
- The SDK now provides plug-and-play middleware with specific naming schemes.

1. Update middleware references from previous custom registrations to the new scheme, as instructed in the [README](README.md#regular-web-applications-1).


#### Update to new `auth0.authorize` middleware, as appropriate
Note: This only applies to backend api application types.

- Previously, the SDK advised you to write your own Access Token handling middleware using the `decodeJWT()` method from the Auth0 PHP SDK.
- The SDK now provides plug-and-play middleware that handles common endpoint authorization, appropriate for most application needs.

1. Remove custom JWT processing or boilerplate code, particularly those referencing `decodeJWT()` from the old Auth0 PHP SDK releases.
2. Add new `middleware()` calls to your routes that reference the new SDK authorization middleware, as instructed in the [README](README.md#backend-api-applications-1).

#### Upgrade Auth0-PHP dependency from 7 to 8, as appropriate

- Previous versions of the SDK implemented v7 of the Auth0-PHP SDK dependency.
- The SDK now uses Auth0-PHP SDK v8.

If you wrote custom code around the underlying Auth0-PHP, or otherwise made internal calls to the underlying SDK through the Laravel SDK, your application will require further upgrade steps. [Please review the upgrade guide for that SDK here.](https://github.com/auth0/auth0-PHP/blob/main/UPGRADE.md)
