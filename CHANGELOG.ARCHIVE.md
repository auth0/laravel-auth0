# Changelog Archive

This file contains changes for all versions of this package prior to the latest major, 7.0.

The changelog for the latest changes is [CHANGELOG.md](./CHANGELOG.md).

## [6.5.0](https://github.com/auth0/laravel-auth0/tree/6.5.0) (2021-10-15)

### Added

-   Add SDK alias methods for passwordless endpoints [\#228](https://github.com/auth0/laravel-auth0/pull/228)

## [6.4.1](https://github.com/auth0/laravel-auth0/tree/6.4.0) (2021-08-02)

### Fixed

-   Use the fully qualified facade class names [\#215](https://github.com/auth0/laravel-auth0/pull/215)
-   Update auth0-PHP dependency [\#222](https://github.com/auth0/laravel-auth0/pull/222)
-   Pass api_identifier config as audience to Auth0\SDK\Auth0 [\#214](https://github.com/auth0/laravel-auth0/pull/214)

## [6.4.0](https://github.com/auth0/laravel-auth0/tree/6.4.0) (2021-03-25)

### Improved

-   Add support for Auth0 Organizations [\#209](https://github.com/auth0/laravel-auth0/pull/209)

## [6.3.0](https://github.com/auth0/laravel-auth0/tree/6.3.0) (2020-02-18)

### Improved

-   Store changes made to the user object during the onLogin event hook [\#206](https://github.com/auth0/laravel-auth0/pull/206)

### Fixed

-   Avoid throwing an error when calling getUserByUserInfo() during login callback event when the supplied profile is empty/null [\#207](https://github.com/auth0/laravel-auth0/pull/207)

## [6.2.0](https://github.com/auth0/laravel-auth0/tree/6.2.0) (2020-01-15)

### Added

-   Support PHP 8.0 [\#200](https://github.com/auth0/laravel-auth0/pull/200)

### Fixed

-   Fix the missing `return null;` in `getUserByIdentifier` [\#201](https://github.com/auth0/laravel-auth0/pull/201)

## [6.1.0](https://github.com/auth0/laravel-auth0/tree/6.1.0) (2020-09-17)

### Added

-   Support Laravel 8 [\#190](https://github.com/auth0/laravel-auth0/pull/190)

### Fixed

-   Fix composer.json whitespace issue [\#192](https://github.com/auth0/laravel-auth0/pull/192)

## [6.0.1](https://github.com/auth0/laravel-auth0/tree/6.0.1) (2020-04-28)

### Fixed

-   Fix access token decoding and validation [\#183](https://github.com/auth0/laravel-auth0/pull/183)

## [6.0.0](https://github.com/auth0/laravel-auth0/tree/6.0.0) (2020-04-09)

**This is a major release and includes breaking changes!** This release also includes a major version change for the PHP SDK that it relies on. Please see the [migration guide](https://github.com/auth0/auth0-PHP/blob/master/MIGRATE-v5-TO-v7.md) for the PHP SDK for more information.

### Added

-   auth0-PHP 7.0 - State and nonce handling [\#163](https://github.com/auth0/laravel-auth0/issues/163)
-   Implement auth0 guard [\#166](https://github.com/auth0/laravel-auth0/pull/166)

### Improved

-   Use array for Auth0JWTUser and add repo return types [\#176](https://github.com/auth0/laravel-auth0/pull/176)
-   Update PHP SDK to v7.0.0 [\#162](https://github.com/auth0/laravel-auth0/pull/162)
-   Bind SessionState handler interface in container [\#147](https://github.com/auth0/laravel-auth0/pull/147)

### Fixed

-   Fix Laravel session management [\#174](https://github.com/auth0/laravel-auth0/pull/174)
-   Cannot use actingAs unit tests functionality [\#161](https://github.com/auth0/laravel-auth0/issues/161)

## [5.4.0](https://github.com/auth0/laravel-auth0/tree/5.4.0) (2020-03-27)

### Added

-   Laravel 7 support [\#167](https://github.com/auth0/laravel-auth0/pull/167)

### Fixed

-   Laravel 7.0 supported release. [\#171](https://github.com/auth0/laravel-auth0/issues/171)
-   Fixed PHPDocs [\#170](https://github.com/auth0/laravel-auth0/pull/170)

## [5.3.1](https://github.com/auth0/laravel-auth0/tree/5.3.1) (2019-11-14)

### Fixed

-   Setting of state_handler in Auth0Service causes "Invalid state" error [\#154](https://github.com/auth0/laravel-auth0/issues/154)
-   Allow store and state_handler to be passed in from config [\#156](https://github.com/auth0/laravel-auth0/pull/156)
-   Add 'persist_refresh_token' key to laravel-auth0 configuration file. [\#152](https://github.com/auth0/laravel-auth0/pull/152)
-   Replace `setEnvironment` with `setEnvProperty` [\#145](https://github.com/auth0/laravel-auth0/pull/145)

## [5.3.0](https://github.com/auth0/laravel-auth0/tree/5.3.0) (2019-09-26)

### Added

-   Support Laravel 6 [\#139](https://github.com/auth0/laravel-auth0/pull/139)
-   Feature request: Add Laravel 6 support [\#138](https://github.com/auth0/laravel-auth0/issues/138)

### Fixed

-   Use LaravelSessionStore in the SessionStateHandler. [\#135](https://github.com/auth0/laravel-auth0/pull/135)
-   SessionStateHandler should use LaravelSessionStore not SessionStore [\#125](https://github.com/auth0/laravel-auth0/issues/125)

## [5.2.0](https://github.com/auth0/laravel-auth0/tree/5.2.0) (2019-06-27)

### Added

-   Authenticate as a Laravel API user using the Auth0 token [\#129](https://github.com/auth0/laravel-auth0/issues/129)
-   Redirect to previous page after login [\#122](https://github.com/auth0/laravel-auth0/issues/122)
-   Auth0User uses private variables so they cannot be accessed or overridden in child class [\#120](https://github.com/auth0/laravel-auth0/issues/120)
-   API routes broken in auth0-laravel-php-web-app (and in general)? [\#117](https://github.com/auth0/laravel-auth0/issues/117)
-   API returning "token algorithm not supported" [\#116](https://github.com/auth0/laravel-auth0/issues/116)
-   Changing name of user identifier [\#115](https://github.com/auth0/laravel-auth0/issues/115)
-   Possible to use User object functions? [\#114](https://github.com/auth0/laravel-auth0/issues/114)
-   Auth0-PHP@5.3.1 breaks Laravel-Auth0 [\#108](https://github.com/auth0/laravel-auth0/issues/108)
-   Extend Illuminate\Foundation\Auth\User [\#104](https://github.com/auth0/laravel-auth0/issues/104)
-   [Bug] Inconsistencies with the singleton Auth0Service [\#103](https://github.com/auth0/laravel-auth0/issues/103)
-   How do you combine Auth0 Lock with Laravel Auth0? [\#102](https://github.com/auth0/laravel-auth0/issues/102)
-   OnLogin callback question [\#97](https://github.com/auth0/laravel-auth0/issues/97)
-   Add composer.lock file [\#123](https://github.com/auth0/laravel-auth0/pull/123) ([lbalmaceda](https://github.com/lbalmaceda))

### Improved

-   Change private properties to protected [\#132](https://github.com/auth0/laravel-auth0/pull/132)
-   Return null instead of false in Auth0UserProvider. [\#128](https://github.com/auth0/laravel-auth0/pull/128)
-   Change the visibility of the getter method from private to public [\#121](https://github.com/auth0/laravel-auth0/pull/121)
-   Updated required PHP version to 5.4 in composer [\#118](https://github.com/auth0/laravel-auth0/pull/118)
-   Changed arrays to use short array syntax [\#110](https://github.com/auth0/laravel-auth0/pull/110)

### Fixed

-   Fix cachehandler resolving issues [\#131](https://github.com/auth0/laravel-auth0/pull/131)
-   Added the Auth0Service as a singleton through the classname [\#107](https://github.com/auth0/laravel-auth0/pull/107)
-   Fixed typo [\#106](https://github.com/auth0/laravel-auth0/pull/106)

## [5.1.0](https://github.com/auth0/laravel-auth0/tree/5.1.0) (2018-03-20)

### Added

-   AutoDiscovery [\#91](https://github.com/auth0/laravel-auth0/pull/91) ([m1guelpf](https://github.com/m1guelpf))
-   Added guzzle options to config to allow for connection options [\#88](https://github.com/auth0/laravel-auth0/pull/88)

### Improved

-   Change default settings file [\#96](https://github.com/auth0/laravel-auth0/pull/96)
-   Utilise Auth0->Login to ensure state validation [\#90](https://github.com/auth0/laravel-auth0/pull/90)

### Fixed

-   Make code comments gender neutral [\#98](https://github.com/auth0/laravel-auth0/pull/98)
-   Fix README and CHANGELOG [\#99](https://github.com/auth0/laravel-auth0/pull/99)
-   pls change config arg name [\#95](https://github.com/auth0/laravel-auth0/issues/95)

## [5.0.2](https://github.com/auth0/laravel-auth0/tree/5.0.2) (2017-08-30)

### Fixed

-   Use instead of to identify the Auth0 user [\#80](https://github.com/auth0/laravel-auth0/pull/80)

## [5.0.1](https://github.com/auth0/laravel-auth0/tree/5.0.1) (2017-02-23)

### Fixed
-   Fixed `supported_algs` configuration name

## [5.0.0](https://github.com/auth0/laravel-auth0/tree/5.0.0) (2017-02-22)

### Fixed

-   V5: update to auth0 sdk v5 [\#69](https://github.com/auth0/laravel-auth0/pull/69)

## [4.0.8](https://github.com/auth0/laravel-auth0/tree/4.0.8) (2017-01-27)

### Fixed

-   Allow use of RS256 Protocol [\#63](https://github.com/auth0/wp-auth0/issues/63)
-   Add RS256 to the list of supported algorithms [\#62](https://github.com/auth0/wp-auth0/issues/62)
-   allow to configure the algorithm supported for token verification [\#65](https://github.com/auth0/laravel-auth0/pull/65)

## [4.0.7](https://github.com/auth0/laravel-auth0/tree/4.0.7) (2017-01-02)

### Fixed

-   it should pass all the configs to the oauth client [\#64](https://github.com/auth0/laravel-auth0/pull/64)

## [4.0.6](https://github.com/auth0/laravel-auth0/tree/4.0.6) (2016-11-29)

### Fixed

-   Code style & docblocks [\#56](https://github.com/auth0/laravel-auth0/pull/56)
-   Adding accessor to retrieve JWT from Auth0Service [\#58](https://github.com/auth0/laravel-auth0/pull/58)

## [4.0.5](https://github.com/auth0/laravel-auth0/tree/4.0.5) (2016-11-29)

### Fixed

-   Added flag for not encoded tokens + removed example [\#57](https://github.com/auth0/laravel-auth0/pull/57)

## [4.0.4](https://github.com/auth0/laravel-auth0/tree/4.0.4) (2016-11-25)

### Fixed

-   Fixing config type [\#55](https://github.com/auth0/laravel-auth0/pull/55)

## [4.0.2](https://github.com/auth0/laravel-auth0/tree/4.0.2) (2016-10-03)

### Fixed

-   Fixing JWTVerifier [\#54](https://github.com/auth0/laravel-auth0/pull/54)

## [4.0.1](https://github.com/auth0/laravel-auth0/tree/4.0.1) (2016-09-19)

### Fixed

-   Fix error becuase of contract and class with the same name [\#52](https://github.com/auth0/laravel-auth0/pull/52)

## [4.0.0](https://github.com/auth0/laravel-auth0/tree/4.0.0) (2016-09-15)

### Improved

-   Better support for Laravel 5.3: Support for Laravel Passport for token verification
Support of auth0 PHP sdk v4 with JWKs cache

### Fixed

-   Merge pull request #50 from auth0/4.x.x-dev [\#50](https://github.com/auth0/laravel-auth0/pull/50)

## [3.2.1](https://github.com/auth0/laravel-auth0/tree/3.2.1) (2016-09-12)

### Fixed

-   Fix for Laravel 5.2 [\#49](https://github.com/auth0/laravel-auth0/pull/49)

## [3.2.0](https://github.com/auth0/laravel-auth0/tree/3.2.0) (2016-07-11)

### Fixed

-   New optional jwt middleware [\#40](https://github.com/auth0/laravel-auth0/pull/40)

## [3.1.0](https://github.com/auth0/laravel-auth0/tree/3.1.0) (2016-05-02)

### Fixed

-   3.1.0 [\#36](https://github.com/auth0/laravel-auth0/pull/36)

## [3.0.3](https://github.com/auth0/laravel-auth0/tree/3.0.3) (2016-01-28)

### Fixed

-   Tag 2.2.2 breaks on Laravel 5.1 [\#30](https://github.com/auth0/laravel-auth0/issues/30)
-   Conform to 5.2's Authenticatable contract [\#31](https://github.com/auth0/laravel-auth0/pull/31)

## [3.0.2](https://github.com/auth0/laravel-auth0/tree/3.0.2) (2016-01-25)

### Fixed

-   Added optional persistence configuration values [\#29](https://github.com/auth0/laravel-auth0/pull/29)

## [2.2.1](https://github.com/auth0/laravel-auth0/tree/2.2.1) (2016-01-22)

### Fixed

-   Create a logout route [\#25](https://github.com/auth0/laravel-auth0/issues/25)
-   Auth0 SDK checks for null values instead of false [\#27](https://github.com/auth0/laravel-auth0/pull/27)

## [3.0.1](https://github.com/auth0/laravel-auth0/tree/3.0.1) (2016-01-18)

### Fixed

-   updated auth0-php dependency [\#24](https://github.com/auth0/laravel-auth0/pull/24)

## [3.0.0](https://github.com/auth0/laravel-auth0/tree/3.0.0) (2016-01-06)

### Fixed

-   auth0/auth0-php ~1.0 requirement doesn't support latest GuzzleHttp [\#21](https://github.com/auth0/laravel-auth0/issues/21)
-   updated to be compatible with laravel 5.2 [\#23](https://github.com/auth0/laravel-auth0/pull/23)

## [2.2.0](https://github.com/auth0/laravel-auth0/tree/2.2.0) (2015-11-30)

### Fixed

-   updated auth0-php dependency version [\#22](https://github.com/auth0/laravel-auth0/pull/22)
-   Update login.blade.php [\#20](https://github.com/auth0/laravel-auth0/pull/20)

## [2.1.4](https://github.com/auth0/laravel-auth0/tree/2.1.4) (2015-10-27)

### Fixed

-   Middleware contract has been deprecated in 5.1 [\#19](https://github.com/auth0/laravel-auth0/pull/19)
-   Fixed some typo's in the comments. [\#18](https://github.com/auth0/laravel-auth0/pull/18)
-   Removed note about unstable dependency from README [\#17](https://github.com/auth0/laravel-auth0/pull/17)
-   Update composer instructions [\#16](https://github.com/auth0/laravel-auth0/pull/16)
-   Use a tagged release of adoy/oauth2 [\#15](https://github.com/auth0/laravel-auth0/pull/15)

## [2.1.3](https://github.com/auth0/laravel-auth0/tree/2.1.3) (2015-07-17)

### Fixed

-   updated jwt dependency [\#14](https://github.com/auth0/laravel-auth0/pull/14)

## [2.1.2](https://github.com/auth0/laravel-auth0/tree/2.1.2) (2015-05-15)

### Fixed

-   Added override of info headers [\#13](https://github.com/auth0/laravel-auth0/pull/13)

## [2.1.1](https://github.com/auth0/laravel-auth0/tree/2.1.1) (2015-05-12)

### Fixed

-   SDK Client headers spec compliant [\#11](https://github.com/auth0/laravel-auth0/issues/11)
-   Support for Laravel 5? [\#6](https://github.com/auth0/laravel-auth0/issues/6)
-   SDK Client headers spec compliant \#11 [\#12](https://github.com/auth0/laravel-auth0/pull/12)

## [2.1.0](https://github.com/auth0/laravel-auth0/tree/2.1.0) (2015-05-07)

### Fixed

-   Upgrade to auth-php 1.0.0: Added support to API V2 [\#10](https://github.com/auth0/laravel-auth0/pull/10)

## [2.0.0](https://github.com/auth0/laravel-auth0/tree/2.0.0) (2015-04-20)

### Fixed

-   Package V2 for Laravel5 [\#9](https://github.com/auth0/laravel-auth0/pull/9)

## [1.0.8](https://github.com/auth0/laravel-auth0/tree/1.0.8) (2015-04-14)

-   [Full Changelog](https://github.com/auth0/laravel-auth0/compare/1.0.7...1.0.8)

## [1.0.7](https://github.com/auth0/laravel-auth0/tree/1.0.7) (2015-04-13)

### Fixed

-   Fixed the way the access token is pased to the A0User [\#7](https://github.com/auth0/laravel-auth0/pull/7)
-   Update README.md [\#5](https://github.com/auth0/laravel-auth0/pull/5)

## [1.0.6](https://github.com/auth0/laravel-auth0/tree/1.0.6) (2014-08-01)

-   [Full Changelog](https://github.com/auth0/laravel-auth0/compare/1.0.5...1.0.6)

## [1.0.5](https://github.com/auth0/laravel-auth0/tree/1.0.5) (2014-08-01)

### Fixed

-   Problem with normal laravel user table [\#4](https://github.com/auth0/laravel-auth0/issues/4)
-   Update README.md [\#3](https://github.com/auth0/laravel-auth0/pull/3)

## [1.0.4](https://github.com/auth0/laravel-auth0/tree/1.0.4) (2014-05-07)

-   [Full Changelog](https://github.com/auth0/laravel-auth0/compare/1.0.3...1.0.4)

## [1.0.3](https://github.com/auth0/laravel-auth0/tree/1.0.3) (2014-04-21)

-   [Full Changelog](https://github.com/auth0/laravel-auth0/compare/1.0.0...1.0.3)
