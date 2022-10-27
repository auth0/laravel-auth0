# Examples using laravel-auth0

- [Custom user models and repositories](#custom-user-models-and-repositories)
- [Authorizing HTTP tests](#authorizing-http-tests)

## Custom user models and repositories

In Laravel, a User Repository is an interface that sits between your authentication source (Auth0) and core Laravel authentication services. It allows you to shape and manipulate the user model and it's data as you need to.

For example, Auth0's unique identifier is a `string` in the format `auth0|123456abcdef`. If you were to attempt to persist a user to many traditional databases you'd likely encounter an error as, by default, a unique identifier is often exected to be an `integer` rather than a `string` type. A custom user model and repository is a great way to address integration challenges like this.

### Creating a custom user model

Let's setup a custom user model for our application. To do this, let's create a file at `app/Auth/Models/User.php` within our Laravel project. This new class needs to implement the `Illuminate\Contracts\Auth\Authenticatable` interface to be compatible with Laravel's Guard API and this SDK. It must also implement either `Auth0\Laravel\Contract\Model\Stateful\User` or `Auth0\Laravel\Contract\Model\Stateless\User` depending on your application's needs. For example:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Auth0\Laravel\Contract\Model\Stateful\User as StatefulUser;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class User extends \Illuminate\Database\Eloquent\Model implements StatefulUser, AuthenticatableUser
{
    use HasFactory, Notifiable, Authenticatable;

    /**
     * The primary identifier for the user.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'name',
        'email',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [];
}
```

### Creating a Custom User Repository

Now let's create a custom user repository for your application which will return the new new custom model. To do this, create the file `app/Auth/CustomUserRepository.php`. This new class must implment the `Auth0\Laravel\Contract\Auth\User\Repository` interface. This new repository takes in user data returned from Auth0's API, applies it to the `App\Models\User` custom user model created in the previous step, and returns it for use throughout your application.

```php
<?php

declare(strict_types=1);

namespace App\Auth;

class CustomUserRepository implements \Auth0\Laravel\Contract\Auth\User\Repository
{
    public function fromSession(
        array $user
    ): ?\Illuminate\Contracts\Auth\Authenticatable {
        return new \App\Models\User([
            'id' => 'just_a_random_example|' . $user['sub'] ?? $user['user_id'] ?? null,
            'name' => $user['name'],
            'email' => $user['email']
        ]);
    }

    public function fromAccessToken(
        array $user
    ): ?\Illuminate\Contracts\Auth\Authenticatable {
        // Simliar to above. Used for stateless application types.
        return null;
    }
}
```

### Using a Custom User Repository

Finally, update your application's `config/auth.php` file. Within the Auth0 provider, assign a custom `repository` value pointing to your new custom user provider class. For example:

```php
    'providers' => [
        //...

        'auth0' => [
            'driver' => 'auth0',
            'repository' => App\Auth\CustomUserRepository::class
        ],
    ],
```

## Authorizing HTTP tests

If your application does contain HTTP tests which access routes that are protected by the `auth0.authorize` middleware, you can use the trait `Auth0\Laravel\Traits\ActingAsAuth0User` in your tests, which will give you a helper method `actingAsAuth0User(array $attributes=[])` simmilar to Laravels `actingAs` method, that allows you to fake beeing authenticated as a Auth0 user.

The argument `attributes` is optional and you can use it to set any auth0 specific user attributes like scope, sub, azp, iap and so on. If no attributes are set, some default values are used.

### Example with a scope protected route

Let's assume you have a route like the following, that is protected by the scope `read:messages`:
```php
Route::get('/api/private-scoped', function () {
    return response()->json([
        'message' => 'Hello from a private endpoint!',
        'authorized' => Auth::check(),
        'user' => Auth::check() ? json_decode(json_encode((array) Auth::user(), JSON_THROW_ON_ERROR), true) : null,
    ], 200, [], JSON_PRETTY_PRINT);
})->middleware(['auth0.authorize:read:messages']);
```

To be able to test the route from above, the implementation of your test would have to look like this:
```php
use Auth0\Laravel\Traits\ActingAsAuth0User;

public function test_readMessages(){
    $response = $this->actingAsAuth0User([
        "scope"=>"read:messages"
    ])->getJson("/api/private-scoped");

    $response->assertStatus(200);
}
```
