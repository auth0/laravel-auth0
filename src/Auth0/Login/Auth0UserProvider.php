<?php

namespace Auth0\Login;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Auth0\Login\Contract\Auth0UserRepository;
use Auth0\SDK\Exception\CoreException;
use Auth0\SDK\Exception\InvalidTokenException;

/**
 * Service that provides an Auth0\LaravelAuth0\Auth0User stored in the session. This User provider
 * should be used when you don't want to persist the entity.
 */
class Auth0UserProvider implements UserProvider
{
    protected $userRepository;
    protected $auth0;

    /**
     * Auth0UserProvider constructor.
     *
     * @param Auth0UserRepository       $userRepository
     * @param \Auth0\Login\Auth0Service $auth0
     */
    public function __construct(Auth0UserRepository $userRepository, Auth0Service $auth0)
    {
        $this->userRepository = $userRepository;
        $this->auth0 = $auth0;
    }

    /**
     * Lets make the repository take care of returning the user related to the
     * identifier.
     *
     * @param mixed $identifier
     *
     * @return Authenticatable
     */
    public function retrieveByID($identifier)
    {
        return $this->userRepository->getUserByIdentifier($identifier);
    }

    /**
     * @param array $credentials
     *
     * @return bool|Authenticatable
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (!isset($credentials['api_token'])) {
            return false;
        }

        $encUser = $credentials['api_token'];

        try {
            $decodedJWT = $this->auth0->decodeJWT($encUser);
        } catch (CoreException $e) {
            return null;
        } catch (InvalidTokenException $e) {
            return null;
        }

        return $this->userRepository->getUserByDecodedJWT($decodedJWT);
    }

    /**
     * Required method by the UserProviderInterface, we don't implement it.
     */
    public function retrieveByToken($identifier, $token)
    {
        return false;
    }

    /**
     * Required method by the UserProviderInterface, we don't implement it.
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
    }

    /**
     * Required method by the UserProviderInterface, we don't implement it.
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return false;
    }
}
