# Events

**The SDK raises events during the authentication process.** Your application can listen to these events and respond to them if desired. For example, you might want to log the user's information to a database when they log in.

To listen for events, you must first create a listener class. These usually live in your application's `app/Listeners` directory. The following example shows how to listen to the `lluminate\Auth\Events\Login` event:

```php
namespace App\Listeners;

use Illuminate\Auth\Events\Login;

final class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        // Log the event to a database.
    }
}
```

Be sure to register your event listeners in your `app/Providers/EventServiceProvider.php` file, for example:

```php
use Illuminate\Auth\Events\Login;
use App\Listeners\LogSuccessfulLogin;
use Illuminate\Support\Facades\Event;

public function boot(): void
{
    Event::listen(
        Login::class,
        [LogSuccessfulLogin::class, 'handle']
    );
}
```

You can learn more about working with the Laravel event system in the [Laravel documentation](https://laravel.com/docs/events).

## Login Controller Events

During login with the `Auth0\Laravel\Http\Controller\Stateful\Login` controller, the following events may be raised:

| Event                                                   | Description                                                                                  |
| ------------------------------------------------------- | -------------------------------------------------------------------------------------------- |
| `Illuminate\Auth\Events\Login`                          | Raised when a user is logging in. The model of the user is provided with the event.          |
| `Auth0\Laravel\Contract\Event\Stateful\LoginAttempting` | Raised before the login redirect is issued, allowing an opportunity to customize parameters. |

## Callback Controller Events

During callback with the `Auth0\Laravel\Http\Controller\Stateful\Callback` controller, the following events may be raised:

| Event                                                     | Description                                                                                                                                                                                                                                                               |
| --------------------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `Illuminate\Auth\Events\Attempting`                       | Raised when a user is returned to the application after authenticating with Auth0. This is raised before verification of the authentication process begins.                                                                                                               |
| `Illuminate\Auth\Events\Failed`                           | Raised when authentication with Auth0 failed. The reason is provided with the event as an array.                                                                                                                                                                          |
| `Auth0\Laravel\Event\Stateful\AuthenticationFailed`       | Raised when authentication with Auth0 failed. This provides an opportunity to intercept the exception thrown by the middleware, by using the event's `setThrowException()` method to `false`. You can also customize the type of exception thrown using `setException()`. |
| `Illuminate\Auth\Events\Illuminate\Auth\Events\Validated` | Raised when authentication was successful, but immediately before the user's session is established.                                                                                                                                                                      |
| `Auth0\Laravel\Event\Stateful\AuthenticationSucceeded`    | Raised when authentication was successful. The model of the authenticated user is provided with the event.                                                                                                                                                                |

## Logout Controller Events

During logout with the `Auth0\Laravel\Http\Controller\Stateful\Logout` controller, the following events may be raised:

| Event                           | Description                                                                          |
| ------------------------------- | ------------------------------------------------------------------------------------ |
| `Illuminate\Auth\Events\Logout` | Raised when a user is logging out. The model of the user is provided with the event. |

## Authentication Middleware Events

During request handling with any `Auth0\Laravel\Http\Middleware\Stateful` middleware, the following events may be raised:

| Event                                            | Description                                                                        |
| ------------------------------------------------ | ---------------------------------------------------------------------------------- |
| `Auth0\Laravel\Event\Middleware\StatefulRequest` | Raised when a request is being handled by a session-based ('stateful') middleware. |

## Authorization Middleware Events

During request handling with any `Auth0\Laravel\Http\Controller\Stateless` middleware, the following events may be raised:

| Event                                                       | Description                                                                                                     |
| ----------------------------------------------------------- | --------------------------------------------------------------------------------------------------------------- |
| `Auth0\Laravel\Event\Middleware\StatelessRequest`           | Raised when a request is being handled by an access token-based ('stateless') middleware.                       |
| `Auth0\Laravel\Event\Stateless\TokenVerificationAttempting` | Raised before an access token is attempted to be verified. The encoded token string is provided with the event. |
| `Auth0\Laravel\Event\Stateless\TokenVerificationSucceeded`  | Raised when an access token is successfully verified. The decoded token contents are provided with the event.   |
| `Auth0\Laravel\Event\Stateless\TokenVerificationFailed`     | Raised when an access token cannot be verified. The reason (as a string) is provided with the event.            |
