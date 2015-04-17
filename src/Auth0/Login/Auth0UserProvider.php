<?php namespace Auth0\Login;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

/**
 * Service that provides an Auth0\LaravelAuth0\Auth0User stored in the session. This User provider
 * should be used when you don't want to persist the entity.
 */
class Auth0UserProvider implements UserProvider
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
    public function retrieveByToken($identifier, $token) {
        return false;
    }

    /**
     * Required method by the UserProviderInterface, we don't implement it
     */
    public function updateRememberToken(Authenticatable $user, $token) {
    }

    /**
     * Required method by the UserProviderInterface, we don't implement it
     */
    public function validateCredentials(Authenticatable $user, array $credentials) {
        return false;
     }
}