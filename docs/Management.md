# Management API

The Auth0 Laravel SDK lets you call Auth0's [Management API](https://auth0.com/docs/api/management/v2) through the underlying [`auth0/auth0-php`](https://github.com/auth0/auth0-PHP) SDK.

Laravel SDK v8 depends on auth0-php v9, whose Management layer is generated with [Fern](https://buildwithfern.com/). Sub-clients are accessed as properties, requests and responses are typed objects, and list endpoints return an auto-paginating iterator.

## Finding the full API reference

This page documents how to reach and use the Management client from Laravel. It does **not** duplicate the per-endpoint method list. That surface belongs to auth0-php and changes as the API spec evolves. For the complete, authoritative list of every sub-client, method, and request/response class, see the auth0-php reference for the major this SDK depends on (currently v9):

- **auth0-php reference:** <https://github.com/auth0/auth0-PHP/blob/v9/reference.md>
- **auth0-php source:** <https://github.com/auth0/auth0-PHP/tree/v9>
- **Auth0 Management API Explorer:** <https://auth0.com/docs/api/management/v2>

## API Application Authorization

Before making Management API calls you must permit your application to communicate with the Management API. In the [Auth0 Dashboard's API page](https://manage.auth0.com/#/apis/), choose `Auth0 Management API`, select the 'Machine to Machine Applications' tab, authorize your Laravel application, and expand the row to choose the scopes you wish to grant.

With `client_id` and `client_secret` configured, the SDK acquires and caches a Management API token for you automatically, so no separate management token configuration is needed.

## Accessing the Management client

The Management client is available via the `management()` method on the SDK service. Pull the SDK instance from the container, or use the `Auth0` facade.

```php
use Auth0\Laravel\Facade\Auth0;

$management = Auth0::management();
```

`management()` returns the base auth0-php [`ManagementClient`](https://github.com/auth0/auth0-PHP/tree/v9/src/API/Management/Wrapper), so its full surface is available to you. That includes every sub-client as a property (`$management->users`, `$management->clients`, and so on) and the underlying generated client via `$management->getManagement()`. Domain, client ID/secret, and the token cache are taken from your guard's configuration automatically.

### Passing options (timeout, retries, headers)

`management()` accepts an optional array of overrides that are forwarded to the base SDK's `ManagementClientOptions`. Everything is optional. Omit the argument for the common case.

```php
$management = Auth0::management([
    'timeout' => 5.0,                              // request timeout in seconds
    'maxRetries' => 3,                             // retry attempts on transient failures
    'additionalHeaders' => ['X-Request-Id' => $id],
    // 'audience' => 'https://your-tenant/api/v2/', // defaults to your tenant's /api/v2/
    // 'token' => $staticManagementToken,           // skip client-credentials and use a token
    // 'httpClient' => $psr18Client,                // supply your own PSR-18 client
    // 'tokenCache' => $psr6Pool,                   // override the management token cache
]);
```

### Setting option defaults in config

To apply the same options to every `management()` call, add an optional `management` block to `config/auth0.php`. Per-call options override these defaults.

```php
// config/auth0.php
return Configuration::VERSION_2 + [
    // ... guards, routes ...

    'management' => [
        'timeout' => 5.0,
        'maxRetries' => 3,
        'additionalHeaders' => ['X-Request-Id' => 'laravel-app'],
    ],
];
```

If you omit the `management` block entirely, nothing changes and the defaults are simply empty.

## Usage patterns

The four patterns below cover almost everything. For the exact method and class names of a given endpoint, consult the [full API reference](#finding-the-full-api-reference).

### 1. Sub-clients are properties, not methods

```php
$management->users;         // not $management->users()
$management->clients;
$management->connections;
```

### 2. List endpoints return an auto-paginating iterator

Call `list()` and iterate. Subsequent pages are fetched automatically as you go.

```php
use Auth0\SDK\API\Management\Users\Requests\ListUsersRequestParameters;

// Always pass explicit pagination values. See the important note below.
$params = new ListUsersRequestParameters([
    'perPage' => 25,
    'page' => 0,
    'includeTotals' => true,
]);

foreach ($management->users->list($params) as $user) {
    echo $user->getEmail();
}
```

> [!IMPORTANT]
> **Always pass explicit pagination values.** The Fern-generated request-parameter constructor overwrites field-level defaults with `null` when no values are supplied, which can otherwise return empty results.
>
> Not every endpoint paginates the same way. Most (users, clients, roles) are **offset-based**, so pass `perPage`, `page`, and `includeTotals`. Some (organizations, connections, client grants) are **cursor-based**. Their request classes instead expose `take` (page size) and `from` (cursor), so pass an explicit `take`. Check the request class fields (or the [full API reference](#finding-the-full-api-reference)) if a list unexpectedly comes back empty.

### 3. Requests are typed objects (camelCase fields)

Parameters are passed as typed request objects rather than associative arrays, and field names are `camelCase` (e.g. `perPage`, `includeTotals`, `userMetadata`) rather than the API's `snake_case`.

### 4. Responses are typed objects with getters

```php
$user = $management->users->get('auth0|...');

$user->getUserId();
$user->getEmail();
$user->getName();
```

## Worked example: update and read a user

```php
use Auth0\Laravel\Facade\Auth0;
use Auth0\SDK\API\Management\Users\Requests\UpdateUserRequestContent;

Route::get('/colors', function () {
    $colors = ['red', 'blue', 'green', 'black', 'white', 'yellow', 'purple', 'orange', 'pink', 'brown'];

    // Assign the authenticated user a random favorite color via user_metadata.
    Auth0::management()->users->update(
        id: auth()->id(),
        request: new UpdateUserRequestContent([
            'userMetadata' => [
                'color' => $colors[random_int(0, count($colors) - 1)],
            ],
        ]),
    );

    // Read the updated profile back as a typed object.
    $profile = Auth0::management()->users->get(auth()->id());
    $color = $profile?->getUserMetadata()['color'] ?? 'unknown';
    $name = auth()->user()->name;

    return response("Hello {$name}! Your favorite color is {$color}.");
})->middleware('auth');
```

## Notable changes from v7 (auth0-php v8)

If you are migrating from a previous Laravel SDK version, see the [v8 section of the upgrade guide](../UPGRADE.md) for full details. The highlights:

- Sub-client access changed from method calls (`$management->users()`) to property access (`$management->users`).
- List endpoints use `list()` (auto-paginating) instead of `getAll()` + `getResponsePaginator()`.
- Requests and responses are typed objects instead of arrays / PSR-7 messages.
- `grants()` is now the `userGrants` property.
- `usersByEmail()` is now `users->listUsersByEmail()`.
- The `blacklists` endpoint was removed (deprecated by Auth0).

v9 also adds many new sub-clients, such as flows, forms, prompts, keys, sessions, and network ACLs. See the [full API reference](#finding-the-full-api-reference) for the complete set.
