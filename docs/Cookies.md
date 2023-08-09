# Cookies

We strongly recommend using the `database` or `redis` session drivers, but realize this is not always a viable option for all developers or use cases. The Auth0 Laravel SDK supports cookies for storing authentication state, but there are notable drawbacks to be aware of.

## Laravel's Cookie Session Driver

As noted in our [sessions documentation](./Sessions.md), Laravel's `cookie` session driver is not a reliable option for production applications as it suffers from a number of notable drawbacks:

- Browsers impose a size limit of 4 KB on individual cookies, which can quickly be exceeded by storing session data.
- Laravel's cookie driver unfortunately does not "chunk" (split up) larger cookies into multiple cookies, so it is impossible to store more than the noted 4 KB of total session data.
- Most web servers and load balancers require additional configuration to accept and deliver larger cookie headers.

## Auth0 PHP SDK's Custom Cookie Session Handler

The underlying [Auth0 PHP SDK](https://github.com/auth0/auth0-PHP) (which the Auth0 Laravel SDK is built upon) includes a powerful custom cookie session handler that supports chunking of larger cookies. This approach will enable you to securely and reliably store larger authentication states for your users.

It is important to note that this approach is incompatible with [Octane](./Octane.md) due to the way it delivers cookie headers.

To enable this feature, assign a `cookie` string value to the `AUTH0_SESSION_STORAGE` and `AUTH0_TRANSIENT_STORAGE` environment variables (or your `.env` file.)

```ini
# Persistent session data:
AUTH0_SESSION_STORAGE=cookie

# Temporary session data (used only during authentication):
AUTH0_TRANSIENT_STORAGE=cookie
```

This will override the SDK's default behavior of using the Laravel Sessions API, and instead use the integrated Auth0 PHP SDK's custom cookie session handler. Please note:

- When this feature is enabled, all properties of cookie storage (like `sameSite`, `secure`, and so forth) must be configured independently. This approach does not use Laravel's settings. Please refer to the [Auth0 PHP SDK's documentation](https://github.com/auth0/auth0-PHP) for guidance on how to configure these.
- By default your Laravel application's `APP_KEY` will be used to encrypt the cookie data. You can change this by assigning the `AUTH0_COOKIE_SECRET` environment variable (or your `.env` file) a string. If you do this, please ensure you are using an adequately long secure secret.
- Please ensure your server is configured to deliver and accept cookies prefixed with `auth0_session_` and `auth0_transient_` followed by a series of numbers (beginning with 0). These are the divided content body of the authenticated session data.

### Increasing Server Cookies Header Sizes

You may need to configure your web server or load balancer to accept and deliver larger cookie headers. For example, if you are using Nginx you will need to set the `large_client_header_buffers` directive to a value greater than the default of 4 KB.

```nginx
large_client_header_buffers 4 16k;
```

Please refer to your web server or load balancer's documentation for more information.

### Reminder on Octane Compatibility

As noted above, the Auth0 PHP SDK's custom cookie session handler is incompatible with [Octane](./Octane.md) due to the way it delivers cookie headers. If you are using Octane, you must use the Laravel Sessions API with a `database` or `redis` driver.
