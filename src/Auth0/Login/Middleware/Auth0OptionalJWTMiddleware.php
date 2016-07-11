<?php namespace Auth0\Login\Middleware;

class Auth0OptionalJWTMiddleware extends Auth0JWTMiddleware {

    protected function validateToken($token) {
        return true;
    }

}