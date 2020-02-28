<?php

namespace Auth0\Login\Traits;

trait Auth0UserTrait {

    /**
     * @var array
     */
    protected $userInfo;

    /**
     * @var string
     */
    protected $accessToken;

    /**
     * Get the unique identifier for the user.
     *
     * @return string|null
     */
    public function getAuthIdentifier() : ?string
    {
        return $this->userInfo['sub'] ?? null;
    }

    /**
     * Get id field name.
     *
     * @return string
     */
    public function getAuthIdentifierName() : string
    {
        return 'id';
    }

    /**
     * Get the password for the user.
     *
     * @return string|null
     */
    public function getAuthPassword() : ?string
    {
        return $this->accessToken;
    }

    /**
     * @return array|null
     */
    public function getUserInfo() : ?array
    {
        return $this->userInfo;
    }

    /**
     * @param string $token
     *
     * @return void
     */
    public function setAccessToken(string $token) : void
    {
        $this->accessToken = $token;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        return '';
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string  $value
     * @return void
     */
    public function setRememberToken($value) {}

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return '';
    }

    /**
     * Add a generic getter to get all the properties of the userInfo.
     *
     * @param string $name Userinfo key name.
     *
     * @return string|null
     */
    public function __get(string $name) : ?string
    {
        return $this->userInfo[$name] ?? null;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return json_encode($this->userInfo);
    }
}
