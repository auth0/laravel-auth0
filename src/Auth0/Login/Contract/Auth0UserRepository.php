<?php

namespace Auth0\Login\Contract;

interface Auth0UserRepository
{
    /**
     * @param stdClass $jwt with the data provided in the JWT
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getUserByDecodedJWT($jwt);

    /**
     * @param array $userInfo representing the user profile and user accessToken
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getUserByUserInfo($userInfo);

    /**
     * @param $identifier the user id
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function getUserByIdentifier($identifier);
}
