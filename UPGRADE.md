# Upgrade Guide

## v8 Migration Guide

Auth0 Laravel SDK v8 updates the underlying `auth0/auth0-php` dependency to **v9**, whose Management API has been regenerated with [Fern](https://buildwithfern.com/). This is the only significant change in v8.

> **v8 is currently released as a beta** (`8.0.0-beta.x`) while `auth0/auth0-php` v9 is in beta. Install it with `composer require auth0/login:^8.0@beta`.

### What does NOT change

Authentication is untouched. If your application only uses Auth0 for login, logout, callback handling, session management, token validation, guards (`auth0-session`, `auth0-api`), middleware, events, and configuration (`config/auth0.php`), **no code changes are required** — update your dependency and you're done.

### Breaking Changes Summary

- The `auth0/auth0-php` dependency is now **v9 only** (`^9.0`). v8 of auth0-php is no longer supported.
- The Management API surface changed (details below). This affects you only if you call `Auth0::management()` or `$guard->management()`.
- The `deprecated/` compatibility shims (the `Auth0\Login`-era classes retained since the v6→v7 migration) have been **removed**. If you still reference any class under the old `deprecated/` path, migrate to its current equivalent first.

### Requirements

- PHP ≥ 8.2
- Laravel 11, 12, or 13

### Migration Guidance: Management API

The Management API is the one area requiring code changes. The patterns below summarize the move from the handwritten v8 API to the Fern-generated v9 API. For the full auth0-php API reference, see [docs/Management.md](docs/Management.md).

#### 1. Sub-client access: method calls → property access

```php
// BEFORE (v7 / auth0-php v8)
$management = Auth0::management();
$users = $management->users();      // method call

// AFTER (v8 / auth0-php v9)
$management = Auth0::management();
$users = $management->users;        // property access
```

#### 2. List methods: `getAll()` → `list()`, returning an auto-paginating Pager

```php
// BEFORE (v7)
use Auth0\SDK\Utility\HttpResponse;

$response = $management->users()->getAll(['per_page' => 10, 'include_totals' => true]);
$users = HttpResponse::decodeContent($response);
foreach ($users as $user) {
    echo $user['email'];
}

// AFTER (v8)
use Auth0\SDK\API\Management\Users\Requests\ListUsersRequestParameters;

$params = new ListUsersRequestParameters([
    'perPage' => 10,
    'page' => 0,
    'includeTotals' => true,
]);
foreach ($management->users->list($params) as $user) {
    echo $user->getEmail();
}
```

#### 3. Request parameters: arrays → typed objects (camelCase)

Parameter names change from `snake_case` to `camelCase`, and are passed as typed request objects rather than associative arrays.

> **Known issue:** always pass explicit pagination values, and note that some endpoints are cursor-based rather than offset-based. See the pagination note in [docs/Management.md](docs/Management.md#2-list-endpoints-return-an-auto-paginating-iterator) for details.

#### 4. Responses: raw arrays → typed objects with getters

```php
// BEFORE (v7)
$user['email'];
$user['user_id'];

// AFTER (v8)
$user->getEmail();
$user->getUserId();
```

#### 5. Method and sub-client renames

- `$management->grants()` → `$management->userGrants`
- `$management->usersByEmail()` → `$management->users->listUsersByEmail(...)`
- `$management->blacklists()` → **removed** (the endpoint was deprecated by Auth0)
- Single-resource fetches use `->get('id')` as before, but return typed objects.

v9 also adds many new sub-clients (flows, forms, prompts, network ACLs, keys, sessions, and more). See [docs/Management.md](docs/Management.md) for the complete list.

#### 6. Management API token acquisition is now automatic

You no longer need to configure a separate management token. With `client_id` and `client_secret` configured, the SDK acquires and caches a Management API token automatically via client credentials.

#### 7. `management()` returns the base ManagementClient and accepts options

`Auth0::management()` (and `$guard->management()`) now returns the auth0-php v9 `ManagementClient` directly, giving you its full surface. It also accepts an optional array of overrides — `timeout`, `maxRetries`, `additionalHeaders`, `audience`, `token`, `httpClient`, `tokenCache` — forwarded to the base SDK's `ManagementClientOptions`. These can also be set as defaults under a `management` key in `config/auth0.php`.

```php
// No arguments needed for the common case.
Auth0::management()->users->list($params);

// Per-call overrides.
Auth0::management([
    'timeout' => 5.0,
    'maxRetries' => 3,
    'additionalHeaders' => ['X-Request-Id' => $id],
])->clients->list($params);
```

See [docs/Management.md](docs/Management.md) for the full list of options and config defaults.

---

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
