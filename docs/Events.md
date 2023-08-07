# Events

- [Introduction](#introduction)
- [SDK Controller Events](#sdk-controller-events)
  - [Login Events](#login-events)
  - [Callback Events](#callback-events)
  - [Logout Events](#logout-events)
- [Deprecated SDK Events](#deprecated-sdk-events)
  - [Authentication Middleware Events](#authentication-middleware-events)
  - [Authorization Middleware Events](#authorization-middleware-events)

## Introduction

Your application can listen to events raised by the SDK, and respond to them if desired. For example, you might want to log the user's information to a database when they log in.

To listen for these events, you must first create a listener class within your application. These usually live in your `app/Listeners` directory. The following example shows how to listen for the `Illuminate\Auth\Events\Login` event:

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

You should also register your event listeners in your application's `app/Providers/EventServiceProvider.php` file, for example:

```php
use Illuminate\Auth\Events\Login;
use App\Listeners\LogSuccessfulLogin;
use Illuminate\Support\Facades\Event;

public function boot(): void
{
    Event::listen(
        Login::class, // The event class.
        [LogSuccessfulLogin::class, 'handle'] // Your listener class and method.
    );
}
```

You can learn more about working with the Laravel event system in the [Laravel documentation](https://laravel.com/docs/events).

## SDK Controller Events

### Login Events

During user authentication triggered by `Auth0\Laravel\Controllers\LoginController` (the `/login` route, by default) the following events may be raised:

| Event                                  | Description                                                                                  |
| -------------------------------------- | -------------------------------------------------------------------------------------------- |
| `Illuminate\Auth\Events\Login`         | Raised when a user is logging in. The model of the user is provided with the event.          |
| `Auth0\Laravel\Events\LoginAttempting` | Raised before the login redirect is issued, allowing an opportunity to customize parameters. |

### Callback Events

During user authentication callback triggered by `Auth0\Laravel\Controllers\CallbackController` (the `/callback` route, by default) the following events may be raised:

| Event                                          | Description                                                                                                                                                                                                                                                               |
| ---------------------------------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `Illuminate\Auth\Events\Attempting`            | Raised when a user is returned to the application after authenticating with Auth0. This is raised before verification of the authentication process begins.                                                                                                               |
| `Illuminate\Auth\Events\Failed`                | Raised when authentication with Auth0 failed. The reason is provided with the event as an array.                                                                                                                                                                          |
| `Auth0\Laravel\Events\AuthenticationFailed`    | Raised when authentication with Auth0 failed. This provides an opportunity to intercept the exception thrown by the middleware, by using the event's `setThrowException()` method to `false`. You can also customize the type of exception thrown using `setException()`. |
| `Illuminate\Auth\Events\Validated`             | Raised when authentication was successful, but immediately before the user's session is established.                                                                                                                                                                      |
| `Auth0\Laravel\Events\AuthenticationSucceeded` | Raised when authentication was successful. The model of the authenticated user is provided with the event.                                                                                                                                                                |

### Logout Events

During user logout by `Auth0\Laravel\Controllers\LogoutController` (the `/logout` route, by default) the following events may be raised:

| Event                           | Description                                                                          |
| ------------------------------- | ------------------------------------------------------------------------------------ |
| `Illuminate\Auth\Events\Logout` | Raised when a user is logging out. The model of the user is provided with the event. |

## Deprecated SDK Events

The following events are deprecated and will be removed in a future release. They are replaced by the events listed in the previous section.

### Authentication Middleware Events

During request handling with `Auth0\Laravel\Middleware\AuthenticateMiddleware` or `Auth0\Laravel\Middleware\AuthenticateOptionalMiddleware` the following events may be raised:

| Event                                                       | Description                                                                        |
| ----------------------------------------------------------- | ---------------------------------------------------------------------------------- |
| `Auth0\Laravel\Events\Middleware\StatefulMiddlewareRequest` | Raised when a request is being handled by a session-based ('stateful') middleware. |

### Authorization Middleware Events

During request handling with `Auth0\Laravel\Middleware\AuthorizeMiddleware` or `Auth0\Laravel\Middleware\AuthorizeOptionalMiddleware` middleware, the following events may be raised:

| Event                                                        | Description                                                                                                     |
| ------------------------------------------------------------ | --------------------------------------------------------------------------------------------------------------- |
| `Auth0\Laravel\Events\Middleware\StatelessMiddlewareRequest` | Raised when a request is being handled by an access token-based ('stateless') middleware.                       |
| `Auth0\Laravel\Events\TokenVerificationAttempting`           | Raised before an access token is attempted to be verified. The encoded token string is provided with the event. |
| `Auth0\Laravel\Events\TokenVerificationSucceeded`            | Raised when an access token is successfully verified. The decoded token contents are provided with the event.   |
| `Auth0\Laravel\Events\TokenVerificationFailed`               | Raised when an access token cannot be verified. The reason (as a string) is provided with the event.            |
