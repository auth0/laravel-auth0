# Support

Your application must use a [supported Laravel version](#supported-laravel-releases), and your host environment must be running a [maintained PHP version](https://www.php.net/supported-versions.php).

You will also need [Composer](https://getcomposer.org/) and an [Auth0 account](https://auth0.com/signup).

### Supported Laravel Releases

The next major release of Laravel is forecasted for Q1 2025. We anticipate supporting it upon release.

| Laravel                                        | SDK   | PHP                                            | Supported Until                                                                                  |
| ---------------------------------------------- | ----- | ---------------------------------------------- | ------------------------------------------------------------------------------------------------ |
| [11.x](https://laravel.com/docs/11.x/releases) | 7.13+ | [8.3](https://www.php.net/releases/8.3/en.php) | Approx. [March 2026](https://laravel.com/docs/11.x/releases#support-policy) (EOL for Laravel 11) |
|                                                |       | [8.2](https://www.php.net/releases/8.2/en.php) | Approx. [Dec 2025](https://www.php.net/supported-versions.php) (EOL for PHP 8.2)                 |

We strive to support all actively maintained Laravel releases, prioritizing support for the latest major version with our SDK. If a new Laravel major introduces breaking changes, we may have to end support for past Laravel versions earlier than planned.

Affected Laravel versions will still receive security fixes until their end-of-life date, as announced in our release notes.

### Maintenance Releases

The following releases are no longer being updated with new features by Auth0, but will continue to receive security updates through their end-of-life date.

| Laravel                                        | SDK        | PHP                                            | Security Fixes Until                                                                   |
| ---------------------------------------------- | ---------- | ---------------------------------------------- | -------------------------------------------------------------------------------------- |
| [10.x](https://laravel.com/docs/10.x/releases) | 7.5 - 7.12 | [8.3](https://www.php.net/releases/8.3/en.php) | [Feb 2025](https://laravel.com/docs/10.x/releases#support-policy) (EOL for Laravel 10) |
|                                                |            | [8.2](https://www.php.net/releases/8.2/en.php) | [Feb 2025](https://laravel.com/docs/10.x/releases#support-policy) (EOL for Laravel 10) |
|                                                |            | [8.1](https://www.php.net/releases/8.2/en.php) | [Nov 2024](https://www.php.net/supported-versions.php) (EOL for PHP 8.1)               |

### Unsupported Releases

The following releases are unsupported by Auth0. While they may be suitable for some legacy applications, your mileage may vary. We recommend upgrading to a supported version as soon as possible.

| Laravel                                      | SDK        |
| -------------------------------------------- | ---------- |
| [9.x](https://laravel.com/docs/9.x/releases) | 7.0 - 7.12 |
| [8.x](https://laravel.com/docs/8.x/releases) | 7.0 - 7.4  |
| [7.x](https://laravel.com/docs/7.x/releases) | 5.4 - 6.5  |
| [6.x](https://laravel.com/docs/6.x/releases) | 5.3 - 6.5  |
| [5.x](https://laravel.com/docs/5.x/releases) | 2.0 - 6.1  |
| [4.x](https://laravel.com/docs/4.x/releases) | 1.x        |

## Support Policy

The SDK follows the [Laravel support policy](https://laravel.com/docs/master/releases#support-policy) and will be supported until the Laravel version it supports reaches end-of-life, or it is no longer technically feasible to support.

## Getting Support

-   If you believe you've found a bug, please [create an issue on GitHub](https://github.com/auth0/laravel-auth0).
-   For questions and community support, please [join the Auth0 Community](https://community.auth0.com/).
-   For paid support plans, please [contact us directly](https://auth0.com/contact-us).
-   For more information about Auth0 Support, please visit our [Support Center](https://support.auth0.com/).
