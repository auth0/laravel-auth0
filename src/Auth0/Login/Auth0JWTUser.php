<?php

namespace Auth0\Login;

/**
 * This class represents a generic user initialized with the user information
 * given by Auth0 and provides a way to access to the decoded JWT data.
 */
class Auth0JWTUser implements \Illuminate\Contracts\Auth\Authenticatable
{
    private $userInfo;

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
        return $this->userInfo['sub'];
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->userInfo['sub'];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return;
    }

    /**
     */
    public function getRememberToken()
    {
        return;
    }

    /**
     * @param $value
     */
    public function setRememberToken($value)
    {
    }

    /**
     */
    public function getRememberTokenName()
    {
        return;
    }

    /**
     * Add a generic getter to get all the properties of the userInfo.
     *
     * @return the related value or null if it is not set
     */
    public function __get($name)
    {
        if (!array_key_exists($name, $this->userInfo)) {
            return;
        }

        return $this->userInfo[$name];
    }

    /**
     * @return array
     */
    public function getUserInfo()
    {
        return $this->userInfo;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode($this->userInfo);
    }
}
