<?php

namespace Auth0\Login\Contract;

use Illuminate\Contracts\Auth\Authenticatable;

interface Auth0UserRepository
{

    /**
     * @param array $decodedJwt with the data provided in the JWT
     *
     * @return Authenticatable
     */
    public function getUserByDecodedJWT(array $decodedJwt) : Authenticatable;

    /**
     * @param array $userInfo Array representing the user's identity
     *
     * @return Authenticatable
     */
    public function getUserByUserInfo(array $userInfo) : Authenticatable;

    /**
     * @param string|int|null $identifier the user id
     *
     * @return Authenticatable|null
     */
    public function getUserByIdentifier($identifier) : ?Authenticatable;
}
