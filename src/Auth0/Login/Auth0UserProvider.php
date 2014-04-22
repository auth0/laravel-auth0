<?php namespace Auth0\Login;

use Illuminate\Auth\UserProviderInterface;
/**
 * Service that provides an Auth0\Login\Auth0User stored in the session. This User provider
 * should be used when you don't want to persist the entity.
 */
class Auth0UserProvider
    implements UserProviderInterface
{


    public function retrieveByID($identifier) {
        $auth0User = \App::make('auth0')->getUserInfo();
        if ($auth0User && $auth0User->getAuthIdentifier() == $identifier) {
            return $auth0User;
        }

    }

    /**
     * Required method by the UserProviderInterface, we don't implement it
     */
    public function retrieveByCredentials(array $credentials) {
        return false;
    }

    /**
     * Required method by the UserProviderInterface, we don't implement it
     */
    public function validateCredentials(\Illuminate\Auth\UserInterface $user, array $credentials) {
        return false;
     }
}