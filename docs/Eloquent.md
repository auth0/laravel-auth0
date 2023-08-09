# Laravel Eloquent

By default, the SDK does not include any Eloquent models or database support. You can adapt the SDK to your application's needs by adding your own Eloquent models and database support.

## Creating a User Model

Begin by creating a new Eloquent model class. You can use the `make:model` Artisan command to do this. Laravel ships with default user model named `User`, so we'll use the `--force` flag to overwrite it with our custom one.

Please ensure you have a backup of your existing `User` model before running this command, as it will overwrite your existing model.

```bash
php artisan make:model User --force
```

Next, open your `app/Models/User.php` file and modify it match the following example. Notably, we'll add a support for a new `auth0` attribute. This attribute will be used to store the user's Auth0 ID, which is used to uniquely identify the user in Auth0.

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;

class User extends Model implements
    AuthenticatableContract,
    AuthorizableContract
{
    use Authenticatable,
        Authorizable,
        HasFactory;

    protected $fillable = [
        'auth0',
        'name',
        'email',
        'email_verified',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
```

Next, create a migration to update your application's `users` table schema to support these changes. Create a new migration file:

```bash
php artisan make:migration update_users_table --table=users
```

Openly the newly created migration file (found under `database/migrations` and ending in `update_users_table.php`) and update to match the following example:

```php
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('auth0')->nullable();
            $table->boolean('email_verified')->default(false);

            $table->unique('auth0', 'users_auth0_unique');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_auth0_unique');

            $table->dropColumn('auth0');
            $table->dropColumn('email_verified');
        });
    }
};

```

Now run the migration:

```bash
php artisan migrate
```

## Creating a User Repository

You'll need to create a new user repository class to handle the creation and retrieval of your Eloquent user models from your database table.

Create a new repository class in your application at `app/Repositories/UserRepository.php`, and update it to match the following example:

```php
<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Auth0\Laravel\{UserRepositoryAbstract, UserRepositoryContract};
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

        $identifier = $user['sub'] ?? $user['auth0'] ?? null;

        if (null === $identifier) {
            return null;
        }

        return User::where('auth0', $identifier)->first();
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

        // Determine the Auth0 identifier for the user from the $user array.
        $identifier = $user['sub'] ?? $user['auth0'] ?? null;

        // Collect relevant user profile information from the $user array for use later.
        $profile = [
            'auth0' => $identifier,
            'name' => $user['name'] ?? '',
            'email' => $user['email'] ?? '',
            'email_verified' => in_array($user['email_verified'], [1, true], true),
        ];

        // Check if a cache of the user exists in memory to avoid unnecessary database queries.
        $cached = $this->withoutRecording(fn () => Cache::get('auth0_user_' . $identifier));

        if ($cached) {
            // Immediately return a cached user if one exists.
            return $cached;
        }

        $user = null;

        // Check if the user exists in the database by Auth0 identifier.
        if (null !== $identifier) {
            $user = User::where('auth0', $identifier)->first();
        }

        // Optional: if the user does not exist in the database by Auth0 identifier, you could fallback to matching by email.
        if (null === $user && isset($user['email'])) {
            $user = User::where('email', $user['email'])->first();
        }

        // If a user was found, check if any updates to the local record are required.
        if (null !== $user) {
            $updates = [];

            if ($user->auth0 !== $profile['auth0']) {
                $updates['auth0'] = $profile['auth0'];
            }

            if ($user->name !== $profile['name']) {
                $updates['name'] = $profile['name'];
            }

            if ($user->email !== $profile['email']) {
                $updates['email'] = $profile['email'];
            }

            $emailVerified = in_array($user->email_verified, [1, true], true);

            if ($emailVerified !== $profile['email_verified']) {
                $updates['email_verified'] = $profile['email_verified'];
            }

            if ([] !== $updates) {
                $user->update($updates);
                $user->save();
            }

            if ([] === $updates && null !== $cached) {
                return $user;
            }
        }

        if (null === $user) {
            // Local password column is not necessary or used by Auth0 authentication, but may be expected by some applications/packages.
            $profile['password'] = Hash::make(Str::random(32));

            // Create the user.
            $user = User::create($profile);
        }

        // Cache the user for 30 seconds.
        $this->withoutRecording(fn () => Cache::put('auth0_user_' . $identifier, $user, 30));

        return $user;
    }

    /**
     * Workaround for Laravel Telescope potentially causing an infinite loop.
     * @link https://github.com/auth0/laravel-auth0/tree/main/docs/Telescope.md
     *
     * @param callable $callback
     */
    private function withoutRecording($callback): mixed
    {
        $telescope = '\Laravel\Telescope\Telescope';

        if (class_exists($telescope)) {
            return "$telescope"::withoutRecording($callback);
        }

        return call_user_func($callback);
    }
}
```

Finally, update your application's `config/auth.php` file to configure the SDK to query your new user provider during authentication requests.

```php
'providers' => [
  'auth0-provider' => [
    'driver' => 'auth0.provider',
    'repository' => \App\Repositories\UserRepository::class,
  ],
],
```
