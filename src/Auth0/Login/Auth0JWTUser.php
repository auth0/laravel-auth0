<?php namespace Auth0\Login;

/**
 * This class represents a generic user initialized with the user information
 * given by Auth0.
 *
 */
class Auth0JWTUser implements \Illuminate\Contracts\Auth\Authenticatable {

    private $userInfo;
    function __construct ($userInfo) {
        $this->userInfo = $userInfo;
    }
    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier() {
        return $this->userInfo->sub;
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword() {
        return null;
    }

    public function getRememberToken() {
        return null;
    }

    public function setRememberToken($value) {

    }

    public function getRememberTokenName() {
        return null;
    }

    /**
     * Add a generic getter to get all the properties of the userInfo
     *
     * @return the related value or null if it is not set
     */
    public function __get($name) {

        if (!array_key_exists($name, $this->userInfo)) {
            return null;
        }

        return $this->userInfo[$name];
    }

    public function getUserInfo() {
        return $this->userInfo;
    }

}
