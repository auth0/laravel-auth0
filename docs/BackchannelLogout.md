# Backchannel Logout

The Auth0 Laravel SDK supports [Backchannel Logout](https://auth0.com/docs/authenticate/login/logout/back-channel-logout) from v7.12 onward. To use this feature, some additional configuration is necessary:

1. **Add a new route to your application.** This route must be publicly accessible. Auth0 will use it to send backchannel logout requests to your application. For example:

```php
Route::post('/backchannel', function (Request $request) {
    if ($request->has('logout_token')) {
        app('auth0')->handleBackchannelLogout($request->string('logout_token', '')->trim());
    }
});
```

2. **Configure your Auth0 tenant to use Backchannel Logout.** See the [Auth0 documentation](https://auth0.com/docs/authenticate/login/logout/back-channel-logout/configure-back-channel-logout) for more information on how to do this. Please ensure you point the Logout URI to the backchannel route we just added to your application.

Note: If your application's configuration assigns `false` to the `backchannelLogoutCache` SDK configuration property, this feature will be disabled entirely.
