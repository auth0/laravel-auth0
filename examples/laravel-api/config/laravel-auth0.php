<?php

return array(

    /*
    |--------------------------------------------------------------------------
    |   Your auth0 domain
    |--------------------------------------------------------------------------
    |   As set in the auth0 administration page
    |
    */

    'domain'        => getenv('AUTH0_DOMAIN'),
    /*
    |--------------------------------------------------------------------------
    |   Your APP id
    |--------------------------------------------------------------------------
    |   As set in the auth0 administration page
    |
    */

    'client_id'     => getenv('AUTH0_CLIENT_ID'),

    /*
    |--------------------------------------------------------------------------
    |   Your APP secret
    |--------------------------------------------------------------------------
    |   As set in the auth0 administration page
    |
    */
    'client_secret' => getenv('AUTH0_CLIENT_SECRET'),


   /*
    |--------------------------------------------------------------------------
    |   The redirect URI
    |--------------------------------------------------------------------------
    |   Should be the same that the one configure in the route to handle the
    |   'Auth0\Login\Auth0Controller@callback'
    |
    */

    'redirect_uri'  => getenv('AUTH0_CALLBACK_URL')



);
