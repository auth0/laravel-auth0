<?php

namespace Auth0\Login\Tests\Unit;

use Auth0\Login\Auth0JWTUser;
use PHPUnit\Framework\TestCase;

class Auth0JWTUserTest extends TestCase
{
    /**
     * @var Auth0JWTUser
     */
    protected $auth0JwtUser;

    public function setUp()
    {
        parent::setUp();
        $this->auth0JwtUser = new Auth0JWTUser((object)[
            "name" => "John Doe",
            "iss" => "http://auth0.com",
            "sub" => "someone@example.com",
            "aud" => "http://example.com",
            "exp" => 1357000000
        ]);
    }

    public function testAuthIdentifierNameIsSubjectOfJWTToken()
    {
        $this->assertEquals('someone@example.com', $this->auth0JwtUser->getAuthIdentifierName());
    }

    public function testAuthIdentifierIsSubjectOfJWTToken()
    {
        $this->assertEquals('someone@example.com', $this->auth0JwtUser->getAuthIdentifier());
    }

    public function testGetAuthPasswordWillNotReturnAnything()
    {
        $this->assertEquals('', $this->auth0JwtUser->getAuthPassword());
    }

    public function testObjectHoldsNoRememberTokenInformation()
    {
        $this->auth0JwtUser->setRememberToken('testing123');

        $this->assertEquals('', $this->auth0JwtUser->getRememberToken());
        $this->assertEquals('', $this->auth0JwtUser->getRememberTokenName());
    }

    public function testGettersCanReturnTokenClaims()
    {
        // Retrieve issuer claim
        $this->assertEquals('http://auth0.com', $this->auth0JwtUser->iss);
    }
}
