<?php namespace Auth0\Login\Contract;

interface Auth0UserRepository {

    public function getUserByDecodedJWT($jwt);
    public function getUserByUserInfo($userInfo);
    public function getUserByIdentifier($identifier);

}