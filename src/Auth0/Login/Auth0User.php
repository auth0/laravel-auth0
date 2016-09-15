<?php namespace Auth0\Login;

/**
 * This class represents a generic user initialized with the user information
 * given by Auth0 and provides a way to access to the user profile.
 *
 */
class Auth0User implements \Illuminate\Contracts\Auth\Authenticatable {

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
     * Get id field name.
     *
     * @return string
     */
    public function getAuthIdentifierName() {
        return 'id';
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

    public function __toString() {
        return json_encode($this->userInfo);
    }

}
