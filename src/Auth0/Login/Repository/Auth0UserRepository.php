<?php namespace Auth0\Login\Repository;
use Auth0\Login\Auth0User;
use Auth0\Login\Auth0JWTUser;

/**
 * Created by PhpStorm.
 * User: germanlena
 * Date: 4/20/15
 * Time: 11:10 AM
 */

class Auth0UserRepository implements \Auth0\Login\Contract\Auth0UserRepository {

    public function getUserByDecodedJWT($jwt) {
        return new Auth0JWTUser($jwt);
    }

    public function getUserByUserInfo($userInfo) {
        return new Auth0User($userInfo['profile'], $userInfo['accessToken']);
    }

    public function getUserByIdentifier($identifier) {
        //Get the user info of the user logged in (probably in session)
        $userInfo = \App::make('auth0')->getUserInfo();

        // build the user
        $auth0User = $this->getUserByUserInfo($userInfo);

        // it is not the same user as logged in, it is not valid
        if ($auth0User && $auth0User->getAuthIdentifier() == $identifier) {
            return $auth0User;
        }
    }

}