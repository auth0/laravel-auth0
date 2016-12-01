<?php

namespace Auth0\Login\Middleware;

class Auth0OptionalJWTMiddleware extends Auth0JWTMiddleware
{
    /**
     * @param $token
     *
     * @return bool
     */
    protected function validateToken($token)
    {
        return true;
    }
}
