<?php namespace Auth0\Login;

use Auth0\Login\Contract\Auth0UserRepository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;

/**
 * Service that provides an Auth0\LaravelAuth0\Auth0User stored in the session. This User provider
 * should be used when you don't want to persist the entity.
 */
class Auth0UserProvider implements UserProvider
{
    protected $userRepository;

    public function __construct(Auth0UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    /**
     * Lets make the repository take care of returning the user related to the
     * identifier
     * @param mixed $identifier
     * @return Authenticatable
     */
    public function retrieveByID($identifier) {
        return $this->userRepository->getUserByIdentifier($identifier);
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
