<?php

namespace Auth0\Login\Contract;

use Illuminate\Contracts\Auth\Authenticatable;

interface Auth0UserRepository
{

    /**
     * @param array $jwt Decoded JWT
     *
     * @return Authenticatable
     */
    public function getUserByDecodedJWT(array $jwt) : Authenticatable;

    /**
     * @param array $userInfo Array representing the user's identity
     *
     * @return Authenticatable
     */
    public function getUserByUserInfo(array $userInfo) : Authenticatable;

    /**
     * @param string|null $identifier User identifier
     *
     * @return Authenticatable|null
     */
    public function getUserByIdentifier(?string $identifier) : ?Authenticatable;
}
