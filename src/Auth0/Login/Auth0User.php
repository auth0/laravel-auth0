<?php

namespace Auth0\Login;

use Auth0\Login\Traits\Auth0UserTrait;
use Illuminate\Contracts\Auth\Authenticatable;

/**
 * This class represents a generic user initialized with the user information
 * given by Auth0 and provides a way to access to the user profile.
 */
class Auth0User implements Authenticatable
{

    use Auth0UserTrait;

    /**
     * Auth0User constructor.
     *
     * @param array $userInfo
     * @param null|string $accessToken
     */
    public function __construct(array $userInfo, ?string $accessToken)
    {
        $this->userInfo = $userInfo;
        $this->accessToken = $accessToken;
    }
}
