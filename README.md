# Laravel Auth0 Plugin
This plugin helps you integrate your Laravel WebApp with [Auth0](https://auth0.com/) to achieve Single Sign On with a few simple steps. You can see an example application [on the example directory](https://github.com/auth0/laravel-auth0/tree/master/examples/laravel-api).

## Installation

Check our docs page to get a complete guide on how to install it in an existing project or download a pre configured seedproject:

* Regular webapp: https://auth0.com/docs/quickstart/webapp/laravel/
* Web API: https://auth0.com/docs/quickstart/backend/php-laravel/

> If you find something wrong in our docs, PR are welcome in our docs repo: https://github.com/auth0/docs

### Setting up a JWKs cache

In the `register` method of your `AppServiceProvider` add:

```php
  $cache = Cache::store('default');

  $this->app->bind(
      '\Auth0\SDK\Helpers\Cache\CacheHandler',
      new \Auth0\SDK\Helpers\Cache\LaravelCacheWrapper($cache));
```

You can implement your own cache strategy by creating a new class that implements the `Auth0\SDK\Helpers\Cache\CacheHandler` contract.

###Laravel 5.2

####Routes
Your routes need to be in the `web` routes group, otherwise it will not be able to use the session storage:

```
Route::group(['middleware' => ['web']], function () {

  Route::get('/auth0/callback', '\Auth0\Login\Auth0Controller@callback');

  Route::get('/', function () {

    if (Auth::check()) dd('LOGGED IN',Auth::user());

    return view('welcome');

  });
});
```

####Auth setup

In your `config/auth.php` file update the providers to use the `auth0` driver:

```
...
    'providers' => [
        'users' => [
            'driver' => 'auth0',
        ],
    ],
...
```

##Laravel Compatibility

The 2.x branch targets Laravel 5.0 and 5.1 compatibility.
The 3.x branch targets Laravel 5.2 compatibility.

If you are working with an older version (Laravel 4.x) you need to point to composer.json to the version 1.0.*

##BC breaks on auth0-php 1.0.0

The latest version of auth0-php has BC breaks.

Besides, laravel-auth0 has full BC, we recommend to check the changes in case you were using it directly. Read the [auth0-php README](https://github.com/auth0/Auth0-PHP).

## Issue Reporting

If you have found a bug or if you have a feature request, please report them at this repository issues section. Please do not report security vulnerabilities on the public GitHub issue tracker. The [Responsible Disclosure Program](https://auth0.com/whitehat) details the procedure for disclosing security issues.

## Author

[Auth0](auth0.com)

## License

This project is licensed under the MIT license. See the [LICENSE](LICENSE.txt) file for more info.
