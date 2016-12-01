<?php

namespace Auth0\Login\Repository;

use Auth0\Login\Auth0User;
use Auth0\Login\Auth0JWTUser;
use Auth0\Login\Contract\Auth0UserRepository as Auth0UserRepositoryContract;

class Auth0UserRepository implements Auth0UserRepositoryContract
{
    /**
     * @param \Auth0\Login\Contract\stdClass $jwt
     *
     * @return Auth0JWTUser
     */
    public function getUserByDecodedJWT($jwt)
    {
        return new Auth0JWTUser($jwt);
    }

    /**
     * @param array $userInfo
     *
     * @return Auth0User
     */
    public function getUserByUserInfo($userInfo)
    {
        return new Auth0User($userInfo['profile'], $userInfo['accessToken']);
    }

    /**
     * @param \Auth0\Login\Contract\the $identifier
     *
     * @return Auth0User|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function getUserByIdentifier($identifier)
    {
        // Get the user info of the user logged in (probably in session)
        $user = \App::make('auth0')->getUser();

        if ($user === null) {
            return;
        }

        // Build the user
        $auth0User = $this->getUserByUserInfo($user);

        // It is not the same user as logged in, it is not valid
        if ($auth0User && $auth0User->getAuthIdentifier() == $identifier) {
            return $auth0User;
        }
    }
}
