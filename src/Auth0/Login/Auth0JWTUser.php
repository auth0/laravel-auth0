<?php

namespace Auth0\Login;

use Auth0\Login\Traits\Auth0UserTrait;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * This class represents a generic user initialized with the user information
 * given by Auth0 and provides a way to access to the decoded JWT data.
 */
class Auth0JWTUser implements Authenticatable
{

    use Auth0UserTrait;

    /**
     * Auth0JWTUser constructor.
     *
     * @param $userInfo
     */
    public function __construct($userInfo)
    {
        $this->userInfo = get_object_vars($userInfo);
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifierName()
    {
        return $this->userInfo['sub'] ?? null;
    }
}
