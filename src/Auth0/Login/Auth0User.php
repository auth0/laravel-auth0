<?php

namespace Auth0\Login;

/**
 * This class represents a generic user initialized with the user information
 * given by Auth0 and provides a way to access to the user profile.
 */
class Auth0User implements \Illuminate\Contracts\Auth\Authenticatable
{

    protected $userInfo;

    protected $accessToken;

    /**
     * Auth0User constructor.
     *
     * @param array       $userInfo
     * @param string|null $accessToken
     */
    public function __construct(array $userInfo, $accessToken)
    {
        $this->userInfo    = $userInfo;
        $this->accessToken = $accessToken;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        if (isset($this->userInfo['sub'])) {
            return $this->userInfo['sub'];
        }

        return $this->userInfo['user_id'];
    }

    /**
     * Get id field name.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id';
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->accessToken;
    }

    /**
     * @return void
     */
    public function getRememberToken()
    {
    }

    /**
     * @param string $value
     */
    public function setRememberToken($value)
    {
    }

    /**
     * @return void
     */
    public function getRememberTokenName()
    {
    }

    /**
     * Add a generic getter to get all the properties of the userInfo.
     *
     * @return null|mixed Returns the related value, or null if not set.
     */
    public function __get($name)
    {
        if (! array_key_exists($name, $this->userInfo)) {
            return;
        }

        return $this->userInfo[$name];
    }

    /**
     * @return mixed
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
