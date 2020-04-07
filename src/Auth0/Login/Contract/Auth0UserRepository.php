<?php

namespace Auth0\Login\Contract;

use \Illuminate\Contracts\Auth\Authenticatable;

interface Auth0UserRepository
{
    /**
     * @param array $jwt with the data provided in the JWT
     *
     * @return Authenticatable
     */
    public function getUserByDecodedJWT(array $jwt);

    /**
     * @param array $userInfo representing the user profile and user accessToken
     *
     * @return Authenticatable
     */
    public function getUserByUserInfo(array $userInfo);

    /**
     * @param $identifier User ID to get
     *
     * @return Authenticatable
     */
    public function getUserByIdentifier($identifier);
}
