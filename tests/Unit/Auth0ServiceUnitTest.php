<?php

namespace Auth0\Login\Tests\Unit;

use Auth0\Login\Auth0Service;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\SDK\Store\SessionStore;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256 as HsSigner;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token;
use PHPUnit\Framework\TestCase;

class Auth0ServiceTest extends TestCase
{
    public static $defaultConfig;

    public function setUp(): void
    {
        parent::setUp();
        self::$defaultConfig = [
            'domain' => '__test_domain__',
            'client_id' => '__test_client_id__',
            'client_secret' => '__test_secret__',
            'redirect_uri' => 'https://example.com/callback',
            'transient_store' => new SessionStore(),
            'api_identifier' => '__test_api_identifier__',
            'supported_algs' => ['HS256']    
        ];
    }

    public function testDecodeJWTReturnsDecodedJWT()
    {
        $service = new Auth0Service(self::$defaultConfig);
        $token = self::getToken();
        
        $this->assertNotEmpty($service->decodeJWT($token));
    }

    public function testThatInvalidTokenExceptionThrownForUnsupportedAlg()
    {
        $service = new Auth0Service(['supported_algs' => ['HS512']] + self::$defaultConfig);
        $token = self::getToken();

        $this->expectException(InvalidTokenException::class);
        $service->decodeJWT($token);
    }

    private static function getToken()
    {
        $builder = new Builder();
        $defaultClaims = [
            'sub' => '__test_sub__',
            'iss' => 'https://__test_domain__/',
            'aud' => '__test_api_identifier__',
            'azp' => '__test_azp__',
            'exp' => time() + 1000,
            'iat' => time() - 1000,
        ];

        foreach ($defaultClaims as $claim => $value) {
            $builder->withClaim($claim, $value);
        }

        return $builder->getToken(new HsSigner(), new Key('__test_secret__'));
    }
}
