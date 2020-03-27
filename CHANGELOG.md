# Change Log

## [5.4.0](https://github.com/auth0/laravel-auth0/tree/5.4.0) (2020-03-27)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/5.3.1...5.4.0)

**Closed issues**
- Laravel 7.0 supported release. [\#171](https://github.com/auth0/laravel-auth0/issues/171)

**Fixed**
- Fixed PHPDocs [\#170](https://github.com/auth0/laravel-auth0/pull/170) ([YAhiru](https://github.com/YAhiru))

**Added**
- Laravel 7 support [\#167](https://github.com/auth0/laravel-auth0/pull/167) ([giannidhooge](https://github.com/giannidhooge))

## [5.3.1](https://github.com/auth0/laravel-auth0/tree/5.3.1) (2019-11-14)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/5.3.0...5.3.1)

**Closed issues**
- Setting of state_handler in Auth0Service causes "Invalid state" error [\#154](https://github.com/auth0/laravel-auth0/issues/154)

**Fixed**
- Allow store and state_handler to be passed in from config [\#156](https://github.com/auth0/laravel-auth0/pull/156) ([joshcanhelp](https://github.com/joshcanhelp))
- Add 'persist_refresh_token' key to laravel-auth0 configuration file. [\#152](https://github.com/auth0/laravel-auth0/pull/152) ([tpenaranda](https://github.com/tpenaranda))
- Replace `setEnvironment` with `setEnvProperty` [\#145](https://github.com/auth0/laravel-auth0/pull/145) ([nstapelbroek](https://github.com/nstapelbroek))

## [5.3.0](https://github.com/auth0/laravel-auth0/tree/5.3.0) (2019-09-26)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/5.2.0...5.3.0)

**Closed issues**
- Feature request: Add Laravel 6 support [\#138](https://github.com/auth0/laravel-auth0/issues/138)
- SessionStateHandler should use LaravelSessionStore not SessionStore [\#125](https://github.com/auth0/laravel-auth0/issues/125)

**Added**
- Support Laravel 6 [\#139](https://github.com/auth0/laravel-auth0/pull/139) ([FreekVR](https://github.com/FreekVR))

**Fixed**
- Use LaravelSessionStore in the SessionStateHandler. [\#135](https://github.com/auth0/laravel-auth0/pull/135) ([nstapelbroek](https://github.com/nstapelbroek))

## [5.2.0](https://github.com/auth0/laravel-auth0/tree/5.2.0) (2019-06-27)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/5.1.0...5.2.0)

**Closed issues**
- Authenticate as a Laravel API user using the Auth0 token [\#129](https://github.com/auth0/laravel-auth0/issues/129)
- Redirect to previous page after login [\#122](https://github.com/auth0/laravel-auth0/issues/122)
- Auth0User uses private variables so they cannot be accessed or overridden in child class [\#120](https://github.com/auth0/laravel-auth0/issues/120)
- API routes broken in auth0-laravel-php-web-app (and in general)? [\#117](https://github.com/auth0/laravel-auth0/issues/117)
- API returning "token algorithm not supported" [\#116](https://github.com/auth0/laravel-auth0/issues/116)
- Changing name of user identifier [\#115](https://github.com/auth0/laravel-auth0/issues/115)
- Possible to use User object functions? [\#114](https://github.com/auth0/laravel-auth0/issues/114)
- Auth0-PHP@5.3.1 breaks Laravel-Auth0 [\#108](https://github.com/auth0/laravel-auth0/issues/108)
- Extend Illuminate\Foundation\Auth\User [\#104](https://github.com/auth0/laravel-auth0/issues/104)
- [Bug] Inconsistencies with the singleton Auth0Service [\#103](https://github.com/auth0/laravel-auth0/issues/103)
- How do you combine Auth0 Lock with Laravel Auth0? [\#102](https://github.com/auth0/laravel-auth0/issues/102)
- OnLogin callback question [\#97](https://github.com/auth0/laravel-auth0/issues/97)

**Added**
- Add composer.lock file [\#123](https://github.com/auth0/laravel-auth0/pull/123) ([lbalmaceda](https://github.com/lbalmaceda))

**Changed**
- Change private properties to protected [\#132](https://github.com/auth0/laravel-auth0/pull/132) ([joshcanhelp](https://github.com/joshcanhelp))
- Return null instead of false in Auth0UserProvider. [\#128](https://github.com/auth0/laravel-auth0/pull/128) ([afreakk](https://github.com/afreakk))
- Change the visibility of the getter method from private to public [\#121](https://github.com/auth0/laravel-auth0/pull/121) ([irieznykov](https://github.com/irieznykov))
- Updated required PHP version to 5.4 in composer [\#118](https://github.com/auth0/laravel-auth0/pull/118) ([dmyers](https://github.com/dmyers))
- Changed arrays to use short array syntax [\#110](https://github.com/auth0/laravel-auth0/pull/110) ([dmyers](https://github.com/dmyers))

**Fixed**
- Fix cachehandler resolving issues [\#131](https://github.com/auth0/laravel-auth0/pull/131) ([deviouspk](https://github.com/deviouspk))
- Added the Auth0Service as a singleton through the classname [\#107](https://github.com/auth0/laravel-auth0/pull/107) ([JCombee](https://github.com/JCombee))
- Fixed typo [\#106](https://github.com/auth0/laravel-auth0/pull/106) ([IvanArjona](https://github.com/IvanArjona))

## [5.1.0](https://github.com/auth0/laravel-auth0/tree/5.1.0) (2018-03-20)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/5.0.2...5.1.0)

**Closed issues**
- pls change config arg name [\#95](https://github.com/auth0/laravel-auth0/issues/95)

**Added**
- AutoDiscovery [\#91](https://github.com/auth0/laravel-auth0/pull/91) ([m1guelpf](https://github.com/m1guelpf))
- Added guzzle options to config to allow for connection options [\#88](https://github.com/auth0/laravel-auth0/pull/88) ([mjmgooch](https://github.com/mjmgooch))

**Changed**
- Change default settings file [\#96](https://github.com/auth0/laravel-auth0/pull/96) ([joshcanhelp](https://github.com/joshcanhelp))
- Utilise Auth0->Login to ensure state validation [\#90](https://github.com/auth0/laravel-auth0/pull/90) ([cocojoe](https://github.com/cocojoe))

**Fixed**
- Make code comments gender neutral [\#98](https://github.com/auth0/laravel-auth0/pull/98) ([devjack](https://github.com/devjack))
- Fix README and CHANGELOG [\#99](https://github.com/auth0/laravel-auth0/pull/99) ([joshcanhelp](https://github.com/joshcanhelp))

## [5.0.2](https://github.com/auth0/laravel-auth0/tree/5.0.2) (2017-08-30)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/5.0.1...5.0.2)

**Merged pull requests:**

- Use instead of to identify the Auth0 user [\#80](https://github.com/auth0/laravel-auth0/pull/80) ([glena](https://github.com/glena))

## [5.0.1](https://github.com/auth0/laravel-auth0/tree/5.0.1) (2017-02-23)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/5.0.0...5.0.1)

Fixed `supported_algs` configuration name

## [5.0.0](https://github.com/auth0/laravel-auth0/tree/5.0.0) (2017-02-22)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/4.0.8...5.0.0)

**Merged pull requests:**

- V5: update to auth0 sdk v5 [\#69](https://github.com/auth0/laravel-auth0/pull/69) ([glena](https://github.com/glena))

## [4.0.8](https://github.com/auth0/laravel-auth0/tree/4.0.8) (2017-01-27)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/4.0.7...4.0.8)

**Closed issues**

- Allow use of RS256 Protocol [\#63](https://github.com/auth0/wp-auth0/issues/63)
- Add RS256 to the list of supported algorithms [\#62](https://github.com/auth0/wp-auth0/issues/62)

**Merged pull requests:**

- allow to configure the algorithm supported for token verification [\#65](https://github.com/auth0/laravel-auth0/pull/65) ([glena](https://github.com/glena))

## [4.0.7](https://github.com/auth0/laravel-auth0/tree/4.0.7) (2017-01-02)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/4.0.6...4.0.7)

**Merged pull requests:**

- it should pass all the configs to the oauth client [\#64](https://github.com/auth0/laravel-auth0/pull/64) ([glena](https://github.com/glena))

## [4.0.6](https://github.com/auth0/laravel-auth0/tree/4.0.6) (2016-11-29)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/4.0.5...4.0.6)

**Merged pull requests:**

- Code style & docblocks [\#56](https://github.com/auth0/laravel-auth0/pull/56) ([seanmangar](https://github.com/seanmangar))
- Adding accessor to retrieve JWT from Auth0Service [\#58](https://github.com/auth0/laravel-auth0/pull/58) ([ryantology](https://github.com/ryantology))

## [4.0.5](https://github.com/auth0/laravel-auth0/tree/4.0.5) (2016-11-29)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/4.0.4...4.0.5)

**Merged pull requests:**

- Added flag for not encoded tokens + removed example [\#57](https://github.com/auth0/laravel-auth0/pull/57) ([glena](https://github.com/glena))

## [4.0.4](https://github.com/auth0/laravel-auth0/tree/4.0.4) (2016-11-25)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/4.0.2...4.0.4)

**Merged pull requests:**

- Fixing config type [\#55](https://github.com/auth0/laravel-auth0/pull/55) ([adamgoose](https://github.com/adamgoose))

## [4.0.2](https://github.com/auth0/laravel-auth0/tree/4.0.2) (2016-10-03)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/4.0.1...4.0.2)

**Merged pull requests:**

- Fixing JWTVerifier [\#54](https://github.com/auth0/laravel-auth0/pull/54) ([adamgoose](https://github.com/adamgoose))

## [4.0.1](https://github.com/auth0/laravel-auth0/tree/4.0.1) (2016-09-19)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/4.0.0...4.0.1)

**Merged pull requests:**

- fix error becuase of contract and class with the same name [\#52](https://github.com/auth0/laravel-auth0/pull/52) ([glena](https://github.com/glena))

## [4.0.0](https://github.com/auth0/laravel-auth0/tree/4.0.0) (2016-09-15)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/3.2.1...4.0.0)

Better support for Laravel 5.3: Support for Laravel Passport for token verification
Support of auth0 PHP sdk v4 with JWKs cache

**Merged pull requests:**

- Merge pull request #50 from auth0/4.x.x-dev [\#50](https://github.com/auth0/laravel-auth0/pull/50) ([glena](https://github.com/glena))

## [3.2.1](https://github.com/auth0/laravel-auth0/tree/3.2.1) (2016-09-12)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/3.2.0...3.2.1)

**Merged pull requests:**

- Fix for Laravel 5.2 [\#49](https://github.com/auth0/laravel-auth0/pull/49) ([dscafati](https://github.com/dscafati))

## [3.2.0](https://github.com/auth0/laravel-auth0/tree/3.2.0) (2016-07-11)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/3.1.0...3.2.0)

**Merged pull requests:**

- New optional jwt middleware [\#40](https://github.com/auth0/laravel-auth0/pull/40) ([glena](https://github.com/glena))

## [3.1.0](https://github.com/auth0/laravel-auth0/tree/3.1.0) (2016-05-02)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/3.0.3...3.1.0)

**Merged pull requests:**

- 3.1.0 [\#36](https://github.com/auth0/laravel-auth0/pull/36) ([glena](https://github.com/glena))

## [3.0.3](https://github.com/auth0/laravel-auth0/tree/3.0.3) (2016-01-28)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/3.0.2...3.0.3)

**Closed issues:**

- Tag 2.2.2 breaks on Laravel 5.1 [\#30](https://github.com/auth0/laravel-auth0/issues/30)

**Merged pull requests:**

- Conform to 5.2's Authenticatable contract [\#31](https://github.com/auth0/laravel-auth0/pull/31) ([ryannjohnson](https://github.com/ryannjohnson))

## [3.0.2](https://github.com/auth0/laravel-auth0/tree/3.0.2) (2016-01-25)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/2.2.1...3.0.2)

**Merged pull requests:**

- Added optional persistence configuration values [\#29](https://github.com/auth0/laravel-auth0/pull/29) ([carnevalle](https://github.com/carnevalle))

## [2.2.1](https://github.com/auth0/laravel-auth0/tree/2.2.1) (2016-01-22)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/3.0.1...2.2.1)

**Closed issues:**

- Create a logout route [\#25](https://github.com/auth0/laravel-auth0/issues/25)

**Merged pull requests:**

- Auth0 SDK checks for null values instead of false [\#27](https://github.com/auth0/laravel-auth0/pull/27) ([thijsvdanker](https://github.com/thijsvdanker))

## [3.0.1](https://github.com/auth0/laravel-auth0/tree/3.0.1) (2016-01-18)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/3.0.0...3.0.1)

**Merged pull requests:**

- updated auth0-php dependency [\#24](https://github.com/auth0/laravel-auth0/pull/24) ([glena](https://github.com/glena))

## [3.0.0](https://github.com/auth0/laravel-auth0/tree/3.0.0) (2016-01-06)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/2.2.0...3.0.0)

**Closed issues:**

- auth0/auth0-php ~1.0 requirement doesn't support latest GuzzleHttp [\#21](https://github.com/auth0/laravel-auth0/issues/21)

**Merged pull requests:**

- updated to be compatible with laravel 5.2 [\#23](https://github.com/auth0/laravel-auth0/pull/23) ([glena](https://github.com/glena))

## [2.2.0](https://github.com/auth0/laravel-auth0/tree/2.2.0) (2015-11-30)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/2.1.4...2.2.0)

**Merged pull requests:**

- updated auth0-php dependency version [\#22](https://github.com/auth0/laravel-auth0/pull/22) ([glena](https://github.com/glena))
- Update login.blade.php [\#20](https://github.com/auth0/laravel-auth0/pull/20) ([Annyv2](https://github.com/Annyv2))

## [2.1.4](https://github.com/auth0/laravel-auth0/tree/2.1.4) (2015-10-27)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/2.1.3...2.1.4)

**Merged pull requests:**

- Middleware contract has been deprecated in 5.1 [\#19](https://github.com/auth0/laravel-auth0/pull/19) ([thijsvdanker](https://github.com/thijsvdanker))
- Fixed some typo's in the comments. [\#18](https://github.com/auth0/laravel-auth0/pull/18) ([thijsvdanker](https://github.com/thijsvdanker))
- Removed note about unstable dependency from README [\#17](https://github.com/auth0/laravel-auth0/pull/17) ([thijsvdanker](https://github.com/thijsvdanker))
- Update composer instructions [\#16](https://github.com/auth0/laravel-auth0/pull/16) ([iWader](https://github.com/iWader))
- Use a tagged release of adoy/oauth2 [\#15](https://github.com/auth0/laravel-auth0/pull/15) ([thijsvdanker](https://github.com/thijsvdanker))

## [2.1.3](https://github.com/auth0/laravel-auth0/tree/2.1.3) (2015-07-17)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/2.1.2...2.1.3)

**Merged pull requests:**

- updated jwt dependency [\#14](https://github.com/auth0/laravel-auth0/pull/14) ([glena](https://github.com/glena))

## [2.1.2](https://github.com/auth0/laravel-auth0/tree/2.1.2) (2015-05-15)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/2.1.1...2.1.2)

**Merged pull requests:**

- Added override of info headers [\#13](https://github.com/auth0/laravel-auth0/pull/13) ([glena](https://github.com/glena))

## [2.1.1](https://github.com/auth0/laravel-auth0/tree/2.1.1) (2015-05-12)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/2.1.0...2.1.1)

**Closed issues:**

- SDK Client headers spec compliant [\#11](https://github.com/auth0/laravel-auth0/issues/11)
- Support for Laravel 5? [\#6](https://github.com/auth0/laravel-auth0/issues/6)

**Merged pull requests:**

- SDK Client headers spec compliant \#11 [\#12](https://github.com/auth0/laravel-auth0/pull/12) ([glena](https://github.com/glena))

## [2.1.0](https://github.com/auth0/laravel-auth0/tree/2.1.0) (2015-05-07)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/2.0.0...2.1.0)

**Merged pull requests:**

- Upgrade to auth-php 1.0.0: Added support to API V2 [\#10](https://github.com/auth0/laravel-auth0/pull/10) ([glena](https://github.com/glena))

## [2.0.0](https://github.com/auth0/laravel-auth0/tree/2.0.0) (2015-04-20)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/1.0.8...2.0.0)

**Merged pull requests:**

- Package V2 for Laravel5 [\#9](https://github.com/auth0/laravel-auth0/pull/9) ([glena](https://github.com/glena))

## [1.0.8](https://github.com/auth0/laravel-auth0/tree/1.0.8) (2015-04-14)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/1.0.7...1.0.8)

## [1.0.7](https://github.com/auth0/laravel-auth0/tree/1.0.7) (2015-04-13)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/1.0.6...1.0.7)

**Merged pull requests:**

- Fixed the way the access token is pased to the A0User [\#7](https://github.com/auth0/laravel-auth0/pull/7) ([glena](https://github.com/glena))
- Update README.md [\#5](https://github.com/auth0/laravel-auth0/pull/5) ([pose](https://github.com/pose))

## [1.0.6](https://github.com/auth0/laravel-auth0/tree/1.0.6) (2014-08-01)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/1.0.5...1.0.6)

## [1.0.5](https://github.com/auth0/laravel-auth0/tree/1.0.5) (2014-08-01)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/1.0.4...1.0.5)

**Closed issues:**

- Problem with normal laravel user table [\#4](https://github.com/auth0/laravel-auth0/issues/4)

**Merged pull requests:**

- Update README.md [\#3](https://github.com/auth0/laravel-auth0/pull/3) ([patekuru](https://github.com/patekuru))

## [1.0.4](https://github.com/auth0/laravel-auth0/tree/1.0.4) (2014-05-07)
[Full Changelog](https://github.com/auth0/laravel-auth0/compare/1.0.3...1.0.4)

## [1.0.3](https://github.com/auth0/laravel-auth0/tree/1.0.3) (2014-04-21)
