<?php

namespace Auth0\Login\Repository;

use Auth0\Login\Auth0User;
use Auth0\Login\Auth0JWTUser;
use Auth0\Login\Contract\Auth0UserRepository as Auth0UserRepositoryContract;
use Illuminate\Contracts\Auth\Authenticatable;

class Auth0UserRepository implements Auth0UserRepositoryContract
{
    /**
     * @param array $decodedJwt
     *
     * @return Auth0JWTUser
     */
    public function getUserByDecodedJWT(array $decodedJwt) : Authenticatable
    {
        return new Auth0JWTUser($decodedJwt);
    }

    /**
     * @param array $userInfo
     *
     * @return Auth0User
     */
    public function getUserByUserInfo(array $userInfo) : Authenticatable
    {
        return new Auth0User($userInfo['profile'], $userInfo['accessToken']);
    }

    /**
     * @param string|integer|null $identifier
     *
     * @return Authenticatable|null
     */
    public function getUserByIdentifier($identifier) : ?Authenticatable
    {
        // Get the user info of the user logged in (probably in session)
        $user = app('auth0')->getUser();

        if ($user === null) {
            return null;
        }

        // Build the user
        $auth0User = $this->getUserByUserInfo($user);

        // It is not the same user as logged in, it is not valid
        if ($auth0User && $auth0User->getAuthIdentifier() == $identifier) {
            return $auth0User;
        }

        return null;
    }
}
