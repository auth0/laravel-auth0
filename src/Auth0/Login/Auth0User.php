<?php namespace Auth0\Login;

/**
 * This class represents a generic user initialized with the user information
 * given by Auth0.
 *
 */
class Auth0User implements \Illuminate\Auth\UserInterface {

    private $userInfo;
    private $accessToken;
    function __construct ($userInfo, $accessToken) {
        $this->userInfo = $userInfo;
        $this->accessToken = $accessToken;
    }
    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier() {
        return $this->userInfo["user_id"];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword() {
        return $this->accessToken;
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
     */
    public function __get($name) {
        return $this->userInfo[$name];
    }

}
