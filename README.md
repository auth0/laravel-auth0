# Laravel Auth0 Plugin

This plugin helps you integrate your [Laravel](https://laravel.com/) WebApp with [Auth0](https://auth0.com/) to achieve Single Sign On with a few simple steps.

## Installation

Check our docs page to get a complete guide on how to install it in an existing project or download a pre-configured seed project:

* Regular webapp: https://auth0.com/docs/quickstart/webapp/laravel
* Web API: https://auth0.com/docs/quickstart/backend/laravel

### Setting up a JWKs cache

In the `register` method of your `AppServiceProvider` add:

```php
  use Illuminate\Support\Facades\Cache;
  
  ...
    public function register()
    {
      ...

      $this->app->bind(
        '\Auth0\SDK\Helpers\Cache\CacheHandler',
        function() {
            static $cacheWrapper = null; 
            if ($cacheWrapper === null) {
                $cache = Cache::store();
                $cacheWrapper = new LaravelCacheWrapper($cache);
            }
            return $cacheWrapper;
        });

        ...
    }
```

You can implement your own cache strategy by creating a new class that implements the `Auth0\SDK\Helpers\Cache\CacheHandler` contract, or just use the cache strategy you want by picking that store with `Cache::store('your_store_name')`;

### Storing users in your database

You can customize the way you handle the users in your application by creating your own `UserRepository`. This class should implement the `Auth0\Login\Contract\Auth0UserRepository` contract. Please see the bottom of the [Laravel Quickstart](https://auth0.com/docs/quickstart/webapp/laravel) guide for the latest example. 

### Laravel 5.2

#### Routes
Your routes need to be in the `web` routes group, otherwise it will not be able to use the session storage:

```php
Route::group(['middleware' => ['web']], function () {

  Route::get('/auth0/callback', '\Auth0\Login\Auth0Controller@callback');

  Route::get('/', function () {

    if (Auth::check()) dd('LOGGED IN',Auth::user());

    return view('welcome');

  });
});
```

#### Auth setup

In your `config/auth.php` file update the providers to use the `auth0` driver:

```php
...
    'providers' => [
        'users' => [
            'driver' => 'auth0',
        ],
    ],
...
```

## Laravel Compatibility

Master targets Laravel 5.5 compatibility.
The 3.x branch targets Laravel 5.2 compatibility.
The 2.x branch targets Laravel 5.0 and 5.1 compatibility.

If you are working with an older version (Laravel 4.x) you need to point to composer.json to the version 1.0.*

## Issue Reporting

If you have found a bug or if you have a feature request, please report them at this repository issues section. Please do not report security vulnerabilities on the public GitHub issue tracker. The [Responsible Disclosure Program](https://auth0.com/whitehat) details the procedure for disclosing security issues.

## Author

[Auth0](https://auth0.com)

## License

This project is licensed under the MIT license. See the [LICENSE](LICENSE.txt) file for more info.
