# Users

## Retrieving User Information

To retrieve information about the currently authenticated user, use the `user()` method on the `Auth0` facade, or the `auth0()` helper.

```php
auth()->user();
```

You can also retrieve information for any user using [the Management API](./Management.md). This also returns extended information about the user, including stored metadata.

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

## Custom User Models and Repositories

The Auth0 Laravel SDK uses the repository pattern to allow the abstraction of potential database operations. This pattern is useful for building completely custom integrations that fit your application's needs.

### Creating a User Repository

Creating a repository is simple: it must implement the `Auth0\Laravel\Auth\User\RepositoryContract` interface, and include two methods:

- `fromSession()` is used to retrieve a user from the application's session.
- `fromAccessToken` is used to retrieve a user from an access token.

The default implementation looks like this:

```php
<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth\User;

use Auth0\Laravel\Auth\User\RepositoryContract;
use Auth0\Laravel\Entities{StatefulUser, StatelessUser};
use Illuminate\Contracts\Auth\Authenticatable;

final class Repository implements RepositoryContract
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
use Auth0\Laravel\Auth\User\RepositoryContract;
use Illuminate\Contracts\Auth\Authenticatable;

final class UserRepository implements RepositoryContract
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

### Creating a User Model

The repository is responsible for retrieving and storing users, but it does not define the user model itself. The SDK provides an abstract user model class that can be extended for building your own implementations, `Auth0\Laravel\Entities\UserAbstract`.

- User models must implement the `Illuminate\Contracts\Auth\Authenticatable` interface, which is required for Laravel's authentication system.
- User models must also implement the `Auth0\Laravel\Entities\UserContract` interface, which is required by the Laravel SDK.

Because the abstract model already fulfills the requirements of these interfaces, you can use it as-is if you do not need to add any additional functionality. Here's an example customer user model that extends the SDK's abstract user model class to support Eloquent:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Auth0\Laravel\Entities\UserAbstract;

final class User extends UserAbstract
{
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

### Integrating the Repository and Model

Once you have created your repository and model, you can integrate them into your application by updating your `config/auth.php` file:

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
