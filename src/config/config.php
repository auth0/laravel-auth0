<?php

return array(

    /*
    |--------------------------------------------------------------------------
    |   Your auth0 domain
    |--------------------------------------------------------------------------
    |   As set in the auth0 administration page
    |
    */

    // 'domain'        => 'XXXX.auth0.com',
    /*
    |--------------------------------------------------------------------------
    |   Your APP id
    |--------------------------------------------------------------------------
    |   As set in the auth0 administration page
    |
    */

    // 'client_id'     => 'XXXX',

    /*
    |--------------------------------------------------------------------------
    |   Your APP secret
    |--------------------------------------------------------------------------
    |   As set in the auth0 administration page
    |
    */
    // 'client_secret' => 'XXXXX',


   /*
    |--------------------------------------------------------------------------
    |   The redirect URI
    |--------------------------------------------------------------------------
    |   Should be the same that the one configure in the route to handle the
    |   'Auth0\Login\Auth0Controller@callback'
    |
    */

    // 'redirect_uri'  => 'http://<host>/auth0/callback',

    /*
    |--------------------------------------------------------------------------
    |   The authorized token issuers
    |--------------------------------------------------------------------------
    |   This is used to verify the decoded tokens when using RS256
    |
    */

    // 'authorized_issuers'  => [ 'https://XXXX.auth0.com/' ],

    /*
    |--------------------------------------------------------------------------
    |   The valid token audiences
    |--------------------------------------------------------------------------
    |   This is used to verify if the decoded tokens are meant to this server
    |
    */

    // 'api_identifier'  => [ ],



);
