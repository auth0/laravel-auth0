# Users

- [User Persistenece](#user-persistenece)
- [Best Practices](#best-practices)
- [Retrieving User Information](#retrieving-user-information)
- [Updating User Information](#updating-user-information)
- [Extending the SDK](#extending-the-sdk)
  - [User Repositories](#user-repositories)
  - [Eloquent User Models](#eloquent-user-models)

## User Persistence

By default the SDK does not persist user information to a database.

- When a user authenticates with your application, the SDK retrieves their profile data from Auth0 and stores it within their session.
- During each subsequent request, the SDK retrieves the stored profile data from the session and constructs a model representing the authenticated user from it.
- This user model is available to your application via the `Auth` facade or `auth()` helper for the duration of the current request.

Later in this guide we'll demonstrate how you can extend this default behavior to persist that profile data to your application's database, if desired.

## Best Practices

Auth0 provides a number of features that can simplify your application's authentication and authorization workflows. It may be helpful to keep the following best practices in mind as you integrate the SDK into your application:

- Treat Auth0 as the single source of truth about your users.
- If you must store user information in a database, store as little as possible. Treat any stored data as a cache, and sync it regularly using [the Management API](./Management.md).
- Always use the [the Management API](./Management.md) to update user information. If you're storing user information in a database, sync those changes to your database as needed, not the other way around.

## Retrieving User Information

To retrieve information about the currently authenticated user, use the `user()` method on the `Auth` facade or `auth()` helper.

```php
auth()->user();
```

You can also retrieve information on any user using [the Management API](./Management.md). This also returns extended information not usually contained in the session state, such as user metadata.

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

## Extending the SDK

### User Repositories

By default the SDK does not store user information in your application's database. Instead, it uses the session to store the user's ID token, and retrieves user information from the token when needed. This is a good default behavior, but it may not be suitable for all applications.

The SDK uses a repository pattern to allow you to customize how user information is stored and retrieved. This allows you to use your own database to cache user information between authentication requests, or to use a different storage mechanism entirely.

#### Creating a User Repository

You can create your own user repository by extending the SDK's `Auth0\Laravel\UserRepositoryAbstract` class implementing the `Auth0\Laravel\UserRepositoryContract` interface. Your repository class need only implement two public methods, both of which should accept a `user` array parameter.

- `fromSession()` to construct a model for an authenticated user. When called, the `user` array will contain the decoded ID token for the authenticated user.
- `fromAccessToken` to construct a model representing an access token request. When called, the `user` array will contain the decoded access token provided with the request.

When these methods are called by the SDK, the `user` array will include all the information your application needs to construct an `Authenticatable` user model.

The default `UserRepository` implementation looks like this:

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

If you're inclined to store user information in an application database, you can expand upon this implementation to retrieve (or create) correlating user records from the database.

```php
<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Auth0\Laravel\{UserRepositoryAbstract, UserRepositoryContract};
use Illuminate\Contracts\Auth\Authenticatable;

final class UserRepository extends UserRepositoryAbstract implements UserRepositoryContract
{
    public function fromAccessToken(array $user): ?Authenticatable
    {
        /*
            $user = [ // Example of a decoded access token
                "iss"   => "https://example.auth0.com/",
                "aud"   => "https://api.example.com/calendar/v1/",
                "sub"   => "auth0|123456",
                "exp"   => 1458872196,
                "iat"   => 1458785796,
                "scope" => "read write",
            ];
        */

        return User::where('auth0', $user['sub'])->first();
    }

    public function fromSession(array $user): ?Authenticatable
    {
        /*
            $user = [ // Example of a decoded ID token
                "iss"         => "http://example.auth0.com",
                "aud"         => "client_id",
                "sub"         => "auth0|123456",
                "exp"         => 1458872196,
                "iat"         => 1458785796,
                "name"        => "Jane Doe",
                "email"       => "janedoe@example.com",
            ];
        */

        $user = User::updateOrCreate(
            attributes: [
                'auth0' => $user['sub'],
            ],
            values: [
                'name' => $user['name'] ?? '',
                'email' => $user['email'] ?? '',
                'email_verified' => $user['email_verified'] ?? false,
            ]
        );

        return $user;
    }
}
```

Note that this example returns a custom user model, `App\Models\User`. You can find an example of this model in the [User Models](#user-models) section below.

#### Registering a Repository

You can override the SDK's default user repository by updating your application's `config/auth.php` file. Simply point the value of the `repository` key to your repository class.

```php
'providers' => [
  'auth0-provider' => [
    'driver' => 'auth0.provider',
    'repository' => \App\Repositories\UserRepository::class,
  ],
],
```

### Eloquent User Models

Please see [Eloquent.md](./Eloquent.md) for guidance on using Eloquent models with the SDK.
