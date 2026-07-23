# GA Revert / Reconcile Checklist

This file tracks changes made on the `v8` branch that are tied to the **beta** phase. There are **two independent GA events**, and each has its own set of follow-ups:

- **A. auth0-php (base SDK) goes GA** — `auth0/auth0-php` v9 ships a stable release on Packagist. Independent of this SDK's own release cadence.
- **B. This SDK (laravel-auth0 v8) goes GA** — the `v8` branch is merged into `main`/`master` and becomes the mainline.

These can happen in either order. Keep this list updated as more beta-only changes land on `v8`. Delete this file once both GA events are complete and all items are resolved.

---

## A. When auth0-php (base SDK) goes GA

Trigger: `auth0/auth0-php` publishes a stable `9.0.0` (no `-beta`) on Packagist.

### A1. Dependency constraint (PENDING — not yet applied)
- **What (planned):** `composer.json` will pin `auth0/auth0-php` at `^9.0@beta` for the beta.
- **On base SDK GA:** Change `^9.0@beta` to `^9.0` (drop the `@beta` stability flag).
- **Also update:** `docker/laravel-sample-app-v9/sdk-composer.v9.json` (same constraint, git-excluded local file).

### A2. Documentation links that point at the `v9` branch
These URLs point at the auth0-php **`v9` branch**, which exists only while v9 is unreleased. Once v9 is on the default branch / tagged, update them (e.g. to `tree/master`, a `9.x` branch, or the tag).

- `docs/Management.md` — "Finding the full API reference" section:
  - `https://github.com/auth0/auth0-PHP/blob/v9/reference.md`
  - `https://github.com/auth0/auth0-PHP/tree/v9`
- `docs/Management.md` — "Accessing the Management client" section:
  - `https://github.com/auth0/auth0-PHP/tree/v9/src/API/Management/Wrapper`
- `docs/Management.md` — the "(currently v9)" phrasing in the reference section intro: update the major if the SDK later moves to a newer auth0-php major.

> The version label lives in **one place** (the "Finding the full API reference" section). Prose elsewhere avoids hardcoding the version, so this is the only spot to touch.

### A3. Upstream auth0-php beta bugs (verify fixed, then simplify)
These workarounds exist because of auth0-php v9 **beta** bugs. Once fixed upstream, re-verify and simplify:
- **Request-parameter constructor nulls field defaults** — documented as the pagination "IMPORTANT" note in `docs/Management.md`. If fixed, the "always pass explicit pagination values" caveat can be softened.
- **`Auth0::management()` TypeError** — the reason our `management()` builds the `ManagementClient` wrapper directly (see B-side note below). If auth0-php's own `Auth0::management()` is fixed to return a working client, re-evaluate whether our wrapper construction is still needed (it likely still is, for the options passthrough — see B4).

---

## B. When this SDK (laravel-auth0 v8) goes GA (merge `v8` → `main`)

Trigger: the `v8` branch is merged into `main`/`master`.

### B1. `.github/workflows/release.yml` — re-enable RL scanner
- **What changed:** The `rl-scanner` job was commented out, and `needs: rl-scanner` on the `release` job was commented out.
- **Why:** The ReversingLabs scanner trust policy only covers `main`/`master`, so it cannot run against the `v8` branch.
- **On GA:** Uncomment the `rl-scanner` job and restore `needs: rl-scanner` on the `release` job.

### B2. `.github/workflows/tests.yml` — remove `v8` branch triggers
- **What changed:** Added `v8` to `push.branches` and added `&& github.ref != 'refs/heads/v8'` to the `concurrency.cancel-in-progress` guard.
- **On GA:** Remove `v8` from `push.branches` and drop the extra `v8` clause from the concurrency guard (revert to `main`-only), unless `v8` is kept as a maintenance line.

### B3. `.github/workflows/sca_scan.yml` — remove `v8` branch triggers
- **What changed:** Added `v8` to both `push.branches` and `pull_request.branches`.
- **On GA:** Remove `v8` from both branch filters (revert to `main`-only), unless `v8` is kept as a maintenance line.

### B4. Version string (PENDING — not yet applied)
- **What (planned):** `.version` and `src/ServiceAbstract.php::VERSION` will carry a `-beta.N` suffix (e.g. `8.0.0-beta.0`).
- **On GA:** Bump to the stable `8.0.0` (no prerelease suffix) for the GA release branch.

### B5. `docker/laravel-sample-app-v9/` — local testing sample app
- **What:** A Docker-based Laravel sample app for local testing against `auth0/auth0-php ^9.0@beta`.
- **Status:** git-excluded via `.git/info/exclude` (NOT `.gitignore`), so it never enters version control.
- **On GA:** No git action needed (it is untracked). Optionally delete the directory locally and remove the `docker/` entry from `.git/info/exclude`.

### B6. `deprecated/` folder removal (PENDING — not yet applied)
- **What (planned):** The `deprecated/` v6→v7 compatibility shims will be deleted, and the `"deprecated/"` path removed from the `composer.json` autoload.
- **On GA:** Ensure this is done as part of cutting `8.0.0` (it is a breaking change appropriate for the major).

### B7. Retire the Fern planning docs
- **What:** `FERN_RELEASE_PLAN.md` and `FERN_SDK_MIGRATION.md` are internal beta-planning docs (kept out of the package via `.gitattributes export-ignore` where applicable).
- **On GA:** Reconcile against final state and either archive or remove; fold anything still user-relevant into `UPGRADE.md`.

---

## Code change that is NOT a revert (keep)

The `management()` change (returns the base `ManagementClient`, accepts an options array) is a **permanent v8 behavior**, not a beta workaround. Do **not** revert it on either GA event. See `FERN_RELEASE_PLAN.md` → "Code Changes Required (Management client)".

---

## Notes
- Release model on `v8` mirrors `main`: feature/fix/chore PRs target `v8`; releases go through a `release/<version>` branch with a "Release <version>" PR, which triggers `release.yml` on merge (the trigger keys off the `release/` head-ref prefix, so it is already branch-agnostic).
- `get-prerelease` action flags any version containing `beta`/`alpha` as a GitHub prerelease automatically.
