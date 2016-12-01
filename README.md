# Laravel Auth0 Plugin
This plugin helps you integrate your Laravel WebApp with [Auth0](https://auth0.com/) to achieve Single Sign On with a few simple steps. You can see an example application [on the example directory](https://github.com/auth0/laravel-auth0/tree/master/examples/laravel-api).

## Installation

Check our docs page to get a complete guide on how to install it in an existing project or download a pre-configured seed project:

* Regular webapp: https://auth0.com/docs/quickstart/webapp/laravel/
* Web API: https://auth0.com/docs/quickstart/backend/php-laravel/

> If you find something wrong in our docs, PR are welcome in our docs repo: https://github.com/auth0/docs

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

You can customize the way you handle the users in your application by creating your own `UserRepository`. This class should implement the `Auth0\Login\Contract\Auth0UserRepository` contract.

```php
<?php 
namespace App\Repository;

use Auth0\Login\Contract\Auth0UserRepository;

class MyCustomUserRepository implements Auth0UserRepository {

    /* This class is used on api authN to fetch the user based on the jwt.*/
    public function getUserByDecodedJWT($jwt) {
      /* 
       * The `sub` claim in the token represents the subject of the token
       * and it is always the `user_id`
       */
      $jwt->user_id = $jwt->sub;

      return $this->upsertUser($jwt);
    }

    public function getUserByUserInfo($userInfo) {
      return $this->upsertUser($userInfo['profile']);
    }

    protected function upsertUser($profile) {

      $user = User::where("auth0id", $profile->user_id)->first();

      if ($user === null) {
          // If not, create one
          $user = new User();
          $user->email = $profile->email; // you should ask for the email scope
          $user->auth0id = $profile->user_id;
          $user->name = $profile->name; // you should ask for the name scope
          $user->save();
      }

      return $user;
    }

    public function getUserByIdentifier($identifier) {
        //Get the user info of the user logged in (probably in session)
        $user = \App::make('auth0')->getUser();

        if ($user===null) return null;

        // build the user
        $user = $this->getUserByUserInfo($user);

        // it is not the same user as logged in, it is not valid
        if ($user && $user->auth0id == $identifier) {
            return $auth0User;
        }
    }

}
```

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
