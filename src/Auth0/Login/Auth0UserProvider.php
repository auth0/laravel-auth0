<?php namespace Auth0\Login;

use Illuminate\Auth\UserProviderInterface;

class Auth0UserProvider
    implements UserProviderInterface
{

    public function retrieveByID($identifier) {
        $auth0User = \App::make('auth0')->getUserInfo();
        if ($auth0User && $auth0User->getAuthIdentifier() == $identifier) {
            return $auth0User;
        }

    }
    public function retrieveByCredentials(array $credentials) {
        return false;
    }

    /**
     * Validate a user against the given credentials.
     *
     * @param \Illuminate\Auth\UserInterface $user
     * @param  array  $credentials
     *
     * @return bool
     */
     public function validateCredentials(\Illuminate\Auth\UserInterface $user, array $credentials) {
        return false;
     }
}