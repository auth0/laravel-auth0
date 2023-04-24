# User Models and Repositories

The Auth0 Laravel SDK uses the repository pattern to allow the abstraction of potential database operations. This pattern is useful for building completely custom integrations that fit your application's needs.

Let's assume you've configured the SDK's user provider in application's `config/auth.php` like this:

```php
/**
 * Register the SDK's User Provider with your application.
 * You should not remove any other entries from this array.
 */
'providers' => [
  'my-example-provider' => [
    'driver' => 'auth0.provider',
    'repository' => \Auth0\Laravel\Auth\User\Repository::class
  ],
],
```

Note the `repository` property — this tells the SDK what class to use for storing and retrieving users. The SDK provides a default implementation, but this does not persist users to an application database. You can add this functionality by using the default implementation as a starting point for building a custom repository of your own.

## Creating a User Repository

Creating a repository is simple: it must implement the `Auth0\Laravel\Contract\Auth\User\Repository` interface, and include two methods:

- `fromSession()` is used to retrieve a user from the application's session.
- `fromAccessToken` is used to retrieve a user from an access token.

The default implementation looks like this:

```php
<?php

declare(strict_types=1);

namespace Auth0\Laravel\Auth\User;

use Auth0\Laravel\Contract\Auth\User\Repository as RepositoryContract;
use Auth0\Laravel\Model\Stateful\User as StatefulUser;
use Auth0\Laravel\Model\Stateless\User as StatelessUser;
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
use Auth0\Laravel\Contract\Auth\User\Repository as RepositoryContract;
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

## Creating a User Model

The repository is responsible for retrieving and storing users, but it does not define the user model itself. The SDK provides an abstract user model class that can be extended for building your own implementations, `Auth0\Laravel\Model\User`.

- User models must implement the `Illuminate\Contracts\Auth\Authenticatable` interface, which is required for Laravel's authentication system.
- User models must also implement the `Auth0\Laravel\Contract\Model\User` interface, which is required by the Laravel SDK.

Because the abstract model already fulfills the requirements of these interfaces, you can use it as-is if you do not need to add any additional functionality. Here's an example customer user model that extends the SDK's abstract user model class to support Eloquent:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Auth0\Laravel\Model\User as Auth0User;

final class User extends Auth0User
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

## Integrating the Repository and Model

Once you have created your repository and model, you can integrate them into your application by updating your `config/auth.php` file:

```php
/**
 * Register the SDK's User Provider with your application.
 * You should not remove any other entries from this array.
 */
'providers' => [
  'my-example-provider' => [
    'driver' => 'auth0.provider',
    'repository' => \App\Repositories\UserRepository::class,
  ],
],
