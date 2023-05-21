# Users

-   [Retrieving User Information](#retrieving-user-information)
-   [Updating User Information](#updating-user-information)
-   [Custom User Repositories](#custom-user-repositories)
-   [Custom User Models](#custom-user-models)

## Retrieving User Information

To retrieve information about the currently authenticated user, use the `user()` method on the `Auth` facade or `auth()` helper.

```php
auth()->user();
```

You can also retrieve information on any user using [the Management API](./Management.md). This also returns extended information not usually contained in the authentication state such as user metadata.

```php
use Auth0\Laravel\Facade\Auth0;

Route::get('/profile', function () {
  $profile = Auth0::management()->users()->get(auth()->id());
  $profile = Auth0::json($profile);

  $name = $profile['name'] ?? 'Unknown';
  $email = $profile['email'] ?? 'Unknown';

  return response("Hello {$name}! Your email address is {$email}.");
})->middleware('auth');
```

## Updating User Information

To update a user's information, use [the Management API](./Management.md).

```php
use Auth0\Laravel\Facade\Auth0;

Route::get('/update', function () {
  Auth0::management()
    ->users()
    ->update(
        id: auth()->id(),
        body: [
            'user_metadata' => [
                'last_visited' => time()
            ]
        ]
    );
})->middleware('auth');
```

## Custom User Repositories

The Auth0 Laravel SDK uses the repository pattern to allow the abstraction of potential database operations. This pattern is useful for building completely custom integrations that fit your application's needs.

### Creating a User Repository

Creating a repository is simple: it must implement the `Auth0\Laravel\UserRepositoryContract` interface, and include two methods:

-   `fromSession()` to construct a model for an authenticated user.
-   `fromAccessToken` to construct a model representing an access token request.

The default implementation looks like this:

```php
<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Users\{StatefulUser, StatelessUser};
use Illuminate\Contracts\Auth\Authenticatable;

final class UserRepository extends UserRepositoryAbstract implements UserRepositoryContract
{
    public function fromAccessToken(array $user): ?Authenticatable
    {
        return new StatelessUser($user);
    }

    public function fromSession(array $user): ?Authenticatable
    {
        return new StatefulUser($user);
    }
}
```

The following example repository uses Laravel's [Eloquent ORM](https://laravel.com/docs/eloquent) to store and retrieve users in a `users` table:

```php
<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Auth0\Laravel\UserRepositoryContract;
use Illuminate\Contracts\Auth\Authenticatable;

final class UserRepository implements UserRepositoryContract
{
    public function fromAccessToken(array $user): ?Authenticatable
    {
        $user = User::firstOrCreate([
            'auth0_id' => $user['sub'],
        ], [
            'name' => $user['name'],
            'email' => $user['email'],
            'email_verified' => $user['email_verified'],
        ]);

        return $user;
    }

    public function fromSession(array $user): ?Authenticatable
    {
        return User::where('auth0_id', $user['sub'])->first();
    }
}
```

### Registering the Repository

The SDK uses it's own repository implementation by default, but you can override this with your own by updating your application's `config/auth.php` file. Simply point the value of the `repository` key to your repository class.

```php
'providers' => [
  'auth0-provider' => [
    'driver' => 'auth0.provider',
    'repository' => \App\Repositories\UserRepository::class,
  ],
],
```

## Custom User Models

The repository is responsible for retrieving and storing users, but does not itself define the models representing those users. To customize these, the SDK provides an abstract class that can be extended, `Auth0\Laravel\Users\UserAbstract`.

User models must implement the following interfaces:

-   `Illuminate\Contracts\Auth\Authenticatable` required by Laravel's authentication APIs.
-   `Auth0\Laravel\Users\UserContract` required by the SDK.

The abstract model already fulfills the requirements of these interfaces, so you can use it as-is if you do not require any additional functionality.

Here's an example customer user model that extends the SDK's abstract user model class to support Eloquent:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Auth0\Laravel\Users\{UserAbstract, UserContract, UserTrait};

final class User extends UserAbstract implements UserContract
{
    use UserTrait;

    protected $table = 'users';

    protected $fillable = [
        'auth0_id',
        'name',
        'email',
        'email_verified',
    ];

    protected $hidden = [
        'auth0_id',
    ];
}
```

#

Once you have created your repository and model, you can integrate them into your application by updating your `config/auth.php` file and pointing the `repository` key to your repository class.

```php
/**
 * Register the SDK's User Provider with your application.
 * You should not remove any other entries from this array.
 */
'providers' => [
  'auth0-provider' => [
    'driver' => 'auth0.provider',
    'repository' => \App\Repositories\UserRepository::class,
  ],
],
```
