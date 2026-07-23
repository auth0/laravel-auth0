# Migration Guide: auth0-php v9 (Fern SDK)

This guide covers the changes required when upgrading `auth0/login` (Laravel Auth0 SDK) to **v8**, which is backed by `auth0/auth0-php` v9 and replaces the handwritten Management API implementation with a Fern-generated SDK.

> This is the internal/detailed companion to the user-facing [UPGRADE.md](UPGRADE.md) v8 section. The `blacklists` endpoint is removed; the old `grants()` client is now `userGrants`.

---

## What Does NOT Change

**Authentication, session management, token validation, middleware, guards, events, configuration, and all non-Management-API functionality remain identical.** No code changes are required for:

- Login / Logout / Callback flows
- `auth0-session` and `auth0-api` guards
- `auth0.authenticate`, `auth0.authorize` middleware
- `config/auth0.php` configuration
- `SdkConfiguration` constructor parameters
- Token validation (`Token::ALGO_RS256`, `Token::TYPE_ACCESS_TOKEN`)
- Session bridge (`StoreInterface`)
- `HttpTelemetry` / `HttpResponse` utilities
- All event classes and exception classes
- User providers and user repositories

**If your application only uses Auth0 for authentication (login/logout) and token-based API authorization, no changes are needed.** Just update `composer.json` and run `composer update`.

---

## What Changes: Management API

If your application accesses the Auth0 Management API via `Auth0::management()` or `$guard->management()`, the following patterns change.

### 1. Sub-client Access: Method Calls → Property Access

```php
// BEFORE (v8)
$management = app('auth0')->management();
$users = $management->users();          // method call
$clients = $management->clients();      // method call
$roles = $management->roles();          // method call

// AFTER (v9 / Fern SDK)
$management = app('auth0')->management();
$users = $management->users;            // property access
$clients = $management->clients;        // property access
$roles = $management->roles;            // property access
```

### 2. Request Parameters: Arrays → Typed Objects

```php
// BEFORE (v8)
$response = $management->users()->getAll([
    'per_page' => 10,
    'page' => 0,
    'include_totals' => true,
]);

// AFTER (v9 / Fern SDK)
use Auth0\SDK\API\Management\Users\Requests\ListUsersRequestParameters;

$params = new ListUsersRequestParameters([
    'perPage' => 10,
    'page' => 0,
    'includeTotals' => true,
]);
$pager = $management->users->list($params);
```

Note: Parameter names change from `snake_case` to `camelCase` in the typed request objects.

### 3. Response Handling: Raw Arrays → Typed Objects

```php
// BEFORE (v8)
use Auth0\SDK\Utility\HttpResponse;

$response = $management->users()->getAll();
$users = HttpResponse::decodeContent($response);

foreach ($users as $user) {
    echo $user['email'];
    echo $user['user_id'];
    echo $user['name'];
}

// AFTER (v9 / Fern SDK)
$pager = $management->users->list($params);

foreach ($pager as $user) {
    echo $user->getEmail();
    echo $user->getUserId();
    echo $user->getName();
}
```

### 4. Pagination: Manual → Automatic

```php
// BEFORE (v8)
$response = $management->users()->getAll(['per_page' => 50, 'include_totals' => true]);
$decoded = HttpResponse::decodeContent($response);
$total = $decoded['total'];

// Manual offset management for next page
$page2 = $management->users()->getAll(['per_page' => 50, 'page' => 1, 'include_totals' => true]);

// Or use the paginator helper
foreach ($management->users()->getResponsePaginator() as $user) {
    // ...
}

// AFTER (v9 / Fern SDK)
// Automatic pagination — just iterate
$params = new ListUsersRequestParameters([
    'perPage' => 50,
    'includeTotals' => true,
]);

foreach ($management->users->list($params) as $user) {
    echo $user->getEmail();
}
```

The Fern SDK's `Pager` automatically fetches subsequent pages as you iterate.

### 5. Method Name Changes

List endpoints use `list()` instead of `getAll()`:

```php
// BEFORE (v8)
$management->users()->getAll();
$management->clients()->getAll();
$management->connections()->getAll();

// AFTER (v9 / Fern SDK)
$management->users->list();
$management->clients->list();
$management->connections->list();
```

Single-resource endpoints use `get()`:

```php
// BEFORE (v8)
$management->users()->get('auth0|123');
$management->clients()->get('client_id_123');

// AFTER (v9 / Fern SDK)
$management->users->get('auth0|123');
$management->clients->get('client_id_123');
```

### 6. Token Acquisition: Now Automatic

```php
// BEFORE (v8) — required explicit management token configuration
// In config/auth0.php:
'management_token' => env('AUTH0_MANAGEMENT_TOKEN'),

// Or manually acquiring a token:
$auth = app('auth0')->authentication();
$response = $auth->clientCredentials(['audience' => '...']);
$token = HttpResponse::decodeContent($response)['access_token'];

// AFTER (v9 / Fern SDK) — automatic via client credentials
// Just configure client_id and client_secret (which you already have for auth).
// The SDK automatically acquires and caches a Management API token.
// No management_token configuration needed.
```

---

## Full Before/After Example

### Listing Users in a Controller

```php
// BEFORE (v8)
use Auth0\SDK\Utility\HttpResponse;

public function listUsers()
{
    $management = app('auth0')->management();
    $response = $management->users()->getAll(['per_page' => 10, 'include_totals' => true]);
    $data = HttpResponse::decodeContent($response);

    return view('users', [
        'users' => $data['users'] ?? $data,
        'total' => $data['total'] ?? count($data),
    ]);
}

// AFTER (v9 / Fern SDK)
use Auth0\SDK\API\Management\Users\Requests\ListUsersRequestParameters;

public function listUsers()
{
    $management = app('auth0')->management();
    $params = new ListUsersRequestParameters([
        'perPage' => 10,
        'page' => 0,
        'includeTotals' => true,
    ]);

    $users = [];
    foreach ($management->users->list($params) as $user) {
        $users[] = [
            'user_id' => $user->getUserId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
        ];
        if (count($users) >= 10) break;
    }

    return view('users', ['users' => $users]);
}
```

---

## Sub-client Name Mapping

| Old SDK (v8) | Fern SDK (v9) |
|---|---|
| `$management->actions()` | `$management->actions` |
| `$management->attackProtection()` | `$management->attackProtection` |
| `$management->blacklists()` | Removed (deprecated endpoint) |
| `$management->clientGrants()` | `$management->clientGrants` |
| `$management->clients()` | `$management->clients` |
| `$management->connections()` | `$management->connections` |
| `$management->deviceCredentials()` | `$management->deviceCredentials` |
| `$management->emails()` | `$management->emails` |
| `$management->emailTemplates()` | `$management->emailTemplates` |
| `$management->grants()` | `$management->userGrants` |
| `$management->guardian()` | `$management->guardian` |
| `$management->jobs()` | `$management->jobs` |
| `$management->logs()` | `$management->logs` |
| `$management->logStreams()` | `$management->logStreams` |
| `$management->organizations()` | `$management->organizations` |
| `$management->resourceServers()` | `$management->resourceServers` |
| `$management->roles()` | `$management->roles` |
| `$management->rules()` | `$management->rules` |
| `$management->stats()` | `$management->stats` |
| `$management->tenants()` | `$management->tenants` |
| `$management->tickets()` | `$management->tickets` |
| `$management->userBlocks()` | `$management->userBlocks` |
| `$management->users()` | `$management->users` |
| `$management->usersByEmail()` | `$management->users->listUsersByEmail()` |

New sub-clients added in the Fern SDK:

- `$management->anomaly`
- `$management->branding`
- `$management->connectionProfiles`
- `$management->customDomains`
- `$management->eventStreams`
- `$management->flows`
- `$management->forms`
- `$management->groups`
- `$management->hooks`
- `$management->keys`
- `$management->networkAcls`
- `$management->prompts`
- `$management->refreshTokens`
- `$management->riskAssessments`
- `$management->rulesConfigs`
- `$management->selfServiceProfiles`
- `$management->sessions`
- `$management->supplementalSignals`
- `$management->tokenExchangeProfiles`
- `$management->userAttributeProfiles`
- `$management->verifiableCredentials`

---

## Known Issues

### Request Parameter Defaults

When calling `list()` without explicit parameters, some endpoints may return empty results. This is due to a bug in the Fern SDK where constructor defaults (`page=0`, `perPage=50`, `includeTotals=true`) are overwritten by the constructor when no values are passed.

**Workaround:** Always pass explicit parameters:

```php
$params = new ListUsersRequestParameters([
    'perPage' => 50,
    'page' => 0,
    'includeTotals' => true,
]);
$pager = $management->users->list($params);
```

This issue is tracked with the Fern team and will be resolved in a future release.

---

## Quick Reference

| Concern | Change Required? |
|---|---|
| Authentication (login/logout/callback) | No |
| Token validation (stateless API) | No |
| Guards (`auth0-session`, `auth0-api`) | No |
| Middleware (`auth0.authenticate`, `auth0.authorize`) | No |
| Configuration (`config/auth0.php`) | No |
| Session management | No |
| Events and exceptions | No |
| Management API: sub-client access | Yes — method → property |
| Management API: request parameters | Yes — arrays → typed objects |
| Management API: response handling | Yes — arrays → typed objects |
| Management API: pagination | Yes — manual → automatic |
| Management API: token acquisition | Simplified (automatic) |
