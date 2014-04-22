# Install and configure
To install this plugin add the following dependency to your composer.json

    "auth0/laravel-auth0" : "1.0.0"

Then, you need to add the plugin in the list of the services providers, located in `app/config/app.php`

    'providers' => array(
        // ...
        'Auth0\Login\LoginServiceProvider',
    );

Optionally, if you want to use the facade called `Auth0` you should also add an alias in the same file

    'aliases' => array(
        // ...
        'Auth0' => 'Auth0\Login\Facade\Auth0'
    );

To configure the plugin, you need to publish the plugin configuration and complete the file `app/config/packages/auth0/login/config.php` using the information of your Auth0 account. To publish the example configuration file use this command

    php artisan config:publish Auth0/Login

The plugin works with the [Laravel security system](http://laravel.com/docs/security), so you can require authorization
on a route using the following code

    Route::get('admin', array('before' => 'auth', function() {
        // ...
    }));

The only difference is that instead of using the `Auth::attempt` mechanism, the plugin expects the user to be authenticated
using a valid `OAuth2.0` callback from Auth0. You can initiate this process using the [Login Widget](https://docs.auth0.com/login-widget2).

You need to select a callback uri and then bind the response to the Auth0Controller, like this

    Route::get('/auth0/callback', 'Auth0\Login\Auth0Controller@callback');

# Defining a user and a user provider

The [Laravel Security System](http://laravel.com/docs/security) needs a *User Object* given by a *User Provider*. With this two abstractions, the user entity can have any structure you like and can be stored anywhere. You configure the *User Provider* indirectly, by selecting an auth driver in `app/config/auth.php`. The default driver is Eloquent, which persists the User model in a database using the ORM.

## Using the auth0 driver

The plugin comes with an authentication driver called auth0. This driver defines a user structure that wraps the [Normalized User Profile](https://docs.auth0.com/user-profile) defined by Auth0, and it doesn't actually persist the object, it just stores it in the session for future calls.

This works fine for basic testing or if you don't really need to persist the user. In any point you can call `Auth::check()` to see if there is a user logged in and `Auth::user()` to get the wrapper with the user information.

## Using other driver

If you want to persist the user you can use the authentication driver you like. The plugin gives you a hook that is called with the *Normalized User Profile* when the callback is succesful, there you can store the user structure as you want. For example, if we use Eloquent, we can add the following code, to persist the user in the database

    Auth0::onLogin(function($auth0User) {
        // See if the user exists
        $user = User::where("auth0id", $auth0User->user_id)->first();
        if ($user === null) {
            // If not, create one
            $user = new User();
            $user->email = $auth0User->email;
            $user->auth0id = $auth0User->user_id;
            $user->nickname = $auth0User->nickname;
            $user->name = $auth0User->name;
            $user->save();
        }
        return $user;
    });

Note that this hook must return the new user, which must implement the `Illuminate\Auth\UserInterface`

