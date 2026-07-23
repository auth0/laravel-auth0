# Release Plan: Laravel Auth0 SDK with Fern-generated auth0-php

This document outlines the internal release plan for updating `auth0/login` (Laravel Auth0 SDK) to work with `auth0/auth0-php` v9 (Fern-generated Management API).

---

## Current State

- **auth0/login**: latest stable is v7.22.0 (on `main`). The v9 work lives on the new **`v8` branch**, which is treated as the working "main" for all beta releases going forward.
- **auth0/auth0-php (Fern)**: v9 line (published to Packagist under the `^9.0@beta` constraint), Management API rewritten with Fern code generation.
- **Release model**: `v8` mirrors `main` — feature/fix/chore PRs target `v8`; releases go through a `release/<version>` branch. First beta is `8.0.0-beta.0`.
- **Local testing**: `docker/laravel-sample-app-v9/` (git-excluded via `.git/info/exclude`) runs the wrapper against `auth0/auth0-php ^9.0@beta` pulled from Packagist, mounting this repo's `src/` live.
- **GA plan**: when `v8` is stable, merge into `main`. Revert items tracked in [GA_REVERT_CHECKLIST.md](GA_REVERT_CHECKLIST.md).

---

## Version Strategy

### auth0/auth0-php

| Phase | Version | Notes |
|---|---|---|
| Beta | `9.0.0-beta.x` | Fern-generated Management API, handwritten auth layer preserved. Consumed via `^9.0@beta`. |
| Stable | `9.0.0` | After beta feedback addressed |

### auth0/login (this repo)

| Phase | Version | Branch | Notes |
|---|---|---|---|
| Current stable | `7.22.0` | `main` | Requires `auth0/auth0-php: ^8.19` |
| Beta | `8.0.0-beta.0` (then `-beta.1`, …) | `v8` | Requires `auth0/auth0-php: ^9.0@beta`; `deprecated/` shims removed |
| GA | `8.0.0` | merged to `main` | Constraint relaxed to `^9.0` once auth0-php v9 is GA |

**Naming for Packagist**: `8.0.0-beta.0` is a valid Composer/SemVer pre-release. Packagist reads it as a beta of `8.0.0` and (with the `get-prerelease` action) it is flagged as a GitHub prerelease automatically. Increment the trailing number for each beta (`-beta.0`, `-beta.1`, …).

**Rationale for a major bump**: unlike the earlier plan (which proposed a minor 7.21.0 with a widened constraint), v8 drops v8-of-auth0-php support entirely and removes the `deprecated/` compatibility layer. That is a breaking change for consumers, so it warrants a major version.

---

## Files to Update

### Required Changes (v8 beta release)

| File | Change | Details |
|---|---|---|
| `composer.json` | Update version constraint | `"auth0/auth0-php": "^8.19"` → `"auth0/auth0-php": "^9.0@beta"` |
| `composer.json` | Drop `deprecated/` autoload path | Remove `"deprecated/"` from the `Auth0\Laravel\` psr-4 array |
| `.version` | Bump version | `7.22.0` → `8.0.0-beta.0` |
| `src/ServiceAbstract.php` | Bump VERSION constant | `'7.22.0'` → `'8.0.0-beta.0'` |
| `CHANGELOG.md` | Add release entry | Document the auth0-php v9 requirement and `deprecated/` removal |
| `deprecated/` | Delete directory | Remove the v6→v7 compatibility shims |

> **Status:** The version/composer bump and `deprecated/` deletion are intentionally **not yet applied** — they are staged for when the release is cut. Docs, workflows, and the local Docker sample app are already updated on `v8`.

### Code Changes Required (Management client)

The original plan assumed zero code changes. That turned out to be wrong: in auth0-php `9.0.0-beta.5`, `Auth0::management()` throws a `TypeError` because the handwritten `Auth0::management()` still constructs the Fern-generated `Management` with an `SdkConfiguration`, while that constructor now expects a `string $token`. The fix is to build the base SDK's `ManagementClient` wrapper (which auto-acquires an M2M token) instead.

The following files were changed so `management()` returns the working `Auth0\SDK\API\Management\Wrapper\ManagementClient`:

| File | Change |
|---|---|
| `src/Entities/InstanceEntityAbstract.php` | `management(array $options = [])` builds `ManagementClient` from the guard's `SdkConfiguration`, merging optional `config('auth0.management')` and per-call `$options` (timeout, maxRetries, additionalHeaders, audience, token, httpClient, tokenCache). |
| `src/Entities/InstanceEntityContract.php` | Signature updated to `management(array $options = []): ManagementClient`. |
| `src/Guards/GuardAbstract.php` | `management()` delegates to the InstanceEntity, returns `ManagementClient`. |
| `src/Guards/GuardContract.php` | Signature updated to `management(array $options = []): ManagementClient`. |
| `src/Auth0.php` | Facade `@method` docblock updated to the new return type. |
| `tests/Unit/Auth/GuardTest.php`, `tests/Unit/ServiceTest.php` | Assert `ManagementClient` instead of `ManagementInterface`. |

This mirrors how the Symfony SDK exposes the v9 Management client (`Service::getManagement()`). All other `Auth0\SDK` imports (Token, HttpResponse, HttpTelemetry, SdkConfiguration, Auth0Interface, StoreInterface, Auth0Exception, exceptions) remain compatible with no changes, verified against the v9 base SDK.

---

## Dependencies & Blockers

### Resolved

- [x] `ManagementInterface` at `Auth0\SDK\Contract\API\ManagementInterface` — resolved via bridge interface in auth0-php (`src/Contract/API/ManagementInterface.php` extends Fern-generated `Auth0\SDK\API\Management\ManagementInterface`)
- [x] `Auth0::management()` with client credentials — Fern team implemented automatic token acquisition via `TokenProvider`
- [x] Pagination defaults (`includeTotals=true`, `page=0`, `perPage=50`) — defaults set in request parameter classes
- [x] 109 sub-client interfaces generated — all leaf clients now have corresponding `*ClientInterface.php`
- [x] All 8 aggregate client interfaces generated (Anomaly, AttackProtection, Emails, Guardian, Keys, RiskAssessments, Tenants, VerifiableCredentials)
- [x] Token.php v8.18.0 security fix (ID token as access token detection) ported

### Known Issues (Non-blocking for Laravel SDK release)

| Issue | Impact | Workaround |
|---|---|---|
| Request parameter constructor overrides field defaults with null | `list()` without explicit params returns empty results | Always pass explicit `perPage`, `page`, `includeTotals` values |
| PHPStan errors in auth0-php (~489) | Does not affect runtime or Laravel SDK tests | Tracked separately in auth0-php repo |
| Blacklists endpoint removed | Users of `$management->blacklists()` need to remove those calls | Endpoint deprecated by Auth0 |

### Blocked On (for auth0-php stable release)

- [ ] Fern team fix: Request parameter constructor default values bug — constructors use `$values['key'] ?? null` which overwrites field-level defaults (reproduced live on `9.0.0-beta.5`)
- [ ] Fern team fix: `Auth0::management()` TypeError — the handwritten `management()` passes an `SdkConfiguration` to a generated `Management` constructor that now expects a `string $token`. Worked around in this SDK by building the `ManagementClient` wrapper directly.
- [ ] auth0-php stable `9.0.0` published to Packagist (currently `9.0.0-beta.5`)

---

## Testing Checklist

### Automated

- [x] `management()` unit tests updated to assert `ManagementClient` (GuardTest, ServiceTest)

### Manual (Completed via the Docker sample app against `9.0.0-beta.5`)

- [x] Login redirect to Auth0 Universal Login
- [x] OAuth callback + code exchange
- [x] Profile page (user info)
- [x] Logout + session clear
- [x] `$guard->management()` returns `Auth0\SDK\API\Management\Wrapper\ManagementClient` (no more TypeError)
- [x] `$management->users->list($params)` returns real user data
- [x] `$management->clients->list($params)` returns real client data (48 items, auto-paginated)
- [x] Typed response objects (`$user->getEmail()`, `$user->getUserId()`, `$client->getName()`)
- [x] Pager iteration surfaces all records across pages
- [x] Cursor-based endpoints (organizations, connections) return data when passed an explicit `take`

### Not Tested (Out of Scope)

- [ ] Backchannel logout (requires Auth0 tenant configuration)
- [ ] All 42 Management API sub-clients (tested representative set: users, clients)
- [ ] Write operations (create, update, delete) against Management API
- [ ] PHPStan / Psalm / Rector / PHP-CS-Fixer against updated dependency (deferred to CI)

---

## Communication Plan

### Release Notes (CHANGELOG.md entry)

```markdown
## [8.0.0-beta.0](https://github.com/auth0/laravel-auth0/tree/8.0.0-beta.0) (YYYY-MM-DD)

**Breaking**

- Now requires `auth0/auth0-php` v9 (Fern-generated Management API). v8 of auth0-php is no longer supported.
- Removed the `deprecated/` v6→v7 compatibility shims.
- `management()` now returns the base `ManagementClient` and accepts an options array.

**Important for Management API users**

If your application calls `Auth0::management()` or `$guard->management()` to access
the Management API, review the [Migration Guide](FERN_SDK_MIGRATION.md) and
[UPGRADE.md](UPGRADE.md) for updated code patterns. Authentication, token validation,
guards, and middleware are unaffected.
```

### What to Communicate to Users

1. **Authentication-only users**: "No changes needed. Run `composer update` and everything works as before."

2. **Management API users**: "The Management API surface has changed. Sub-client access is now via properties (`$management->users`) instead of methods (`$management->users()`). Responses are typed objects with getter methods instead of raw arrays. See the migration guide for before/after examples."

3. **New capabilities**:
   - 20+ new Management API sub-clients (flows, forms, prompts, network ACLs, etc.)
   - Automatic Management API token acquisition (no need to configure `management_token` separately)
   - Built-in pagination via `Pager` iterator
   - Typed request and response objects with IDE autocompletion

---

## Rollback Plan

`8.0.0-beta.x` is a distinct major published from the `v8` branch, so `main` and the `7.x` line are unaffected. If critical issues are found:

1. Users can pin to the `7.x` line (`composer require auth0/login:^7`), which stays on `auth0/auth0-php ^8`.
2. `main` is untouched during the beta, so there is nothing to roll back on the stable line.

---

## Timeline

| Step | Status | Owner |
|---|---|---|
| `v8` branch created from `main` | Done | Auth0 |
| Docs (README, UPGRADE, Management) updated for v9 | Done | Auth0 |
| `.github` workflows adjusted for `v8` branch releases | Done | Auth0 |
| Local Docker sample app built and verified against `9.0.0-beta.5` | Done | Auth0 |
| `management()` fix (returns `ManagementClient`) + tests | Done | Auth0 |
| Version bump to `8.0.0-beta.0` + `deprecated/` removal + composer constraint | Pending (staged) | Auth0 |
| `auth0/login 8.0.0-beta.0` released from `release/8.0.0-beta.0` | Pending | Auth0 |
| auth0-php stable `9.0.0` published | Pending | Auth0/Fern |
| `v8` merged to `main` for GA (see GA_REVERT_CHECKLIST.md) | Pending | Auth0 |
