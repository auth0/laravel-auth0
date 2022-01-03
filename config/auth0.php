<?php

declare(strict_types=1);

return [
    // Should be assigned either 'api', 'management', or 'webapp' to indicate your application's use case for the SDK. Determines what configuration options will be required at initialization.
    'strategy' => env('AUTH0_STRATEGY', 'api'),

    // Auth0 domain for your tenant, found in your Auth0 Application settings.
    'domain' => env('AUTH0_DOMAIN'),

    // If you have configured Auth0 to use a custom domain, configure it here.
    'customDomain' => env('AUTH0_CUSTOM_DOMAIN'),

    // Client ID, found in the Auth0 Application settings.
    'clientId' => env('AUTH0_CLIENT_ID'),

    // Authentication callback URI, as defined in your Auth0 Application settings.
    'redirectUri' => env('AUTH0_REDIRECT_URI', env('APP_URL') . '/callback'),

    // Client Secret, found in the Auth0 Application settings.
    'clientSecret' => env('AUTH0_CLIENT_SECRET'),

    // One or more API identifiers, found in your Auth0 API settings. The SDK uses the first value for building links. If provided, at least one of these values must match the 'aud' claim to validate an ID Token successfully.
    'audience' => env('AUTH0_AUDIENCE', []),

    // One or more Organization IDs, found in your Auth0 Organization settings. The SDK uses the first value for building links. If provided, at least one of these values must match the 'org_id' claim to validate an ID Token successfully.
    'organization' => env('AUTH0_ORGANIZATION', []),

    // The secret used to derive an encryption key for the user identity in a session cookie and to sign the transient cookies used by the login callback.
    'cookieSecret' => env('AUTH0_COOKIE_SECRET', env('APP_KEY')),

    // How long, in seconds, before cookies expire. If set to 0 the cookie will expire at the end of the session (when the browser closes).
    'cookieExpires' => env('COOKIE_EXPIRES', 0),

    // Named routes the SDK may call during stateful requests for redirections.
    'routeLogin' => 'login',
    'routeLogout' => 'logout',
    'routeCallback' => 'callback',
];
