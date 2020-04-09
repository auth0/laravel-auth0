<?php
namespace Auth0\Login\Tests;

use Auth0\Login\Auth0JWTUser;
use Auth0\Login\Auth0Service;
use Auth0\Login\Facade\Auth0 as Auth0Facade;
use Auth0\Login\LoginServiceProvider as Auth0ServiceProvider;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\SDK\Store\SessionStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class Auth0ServiceTest extends OrchestraTestCase
{
    public static $defaultConfig;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$defaultConfig = [
            'domain' => 'test.auth0.com',
            'client_id' => '__test_client_id__',
            'client_secret' => '__test_client_secret__',
            'redirect_uri' => 'https://example.com/callback',
            'transient_store' => new SessionStore(),
        ];
    }

    public function tearDown() : void
    {
        Cache::flush();
    }

    public function testThatServiceUsesSessionStoreByDefault()
    {
        session(['auth0__user' => '__test_user__']);
        $service = new Auth0Service(self::$defaultConfig);
        $user = $service->getUser();

        $this->assertArrayHasKey('profile', $user);
        $this->assertEquals('__test_user__', $user['profile']);
    }

    public function testThatServiceSetsEmptyStoreFromConfigAndConstructor()
    {
        session(['auth0__user' => '__test_user__']);

        $service = new Auth0Service(self::$defaultConfig + ['store' => false]);
        $this->assertNull($service->getUser());

        $service = new Auth0Service(self::$defaultConfig);
        $this->assertIsArray($service->getUser());
    }

    public function testThatServiceLoginReturnsRedirect()
    {
        $service = new Auth0Service(self::$defaultConfig);
        $redirect = $service->login();

        $this->assertInstanceOf( RedirectResponse::class, $redirect );

        $targetUrl = parse_url($redirect->getTargetUrl());

        $this->assertEquals('test.auth0.com', $targetUrl['host']);

        $targetUrlQuery = explode('&', $targetUrl['query']);

        $this->assertContains('redirect_uri=https%3A%2F%2Fexample.com%2Fcallback', $targetUrlQuery);
        $this->assertContains('client_id=__test_client_id__', $targetUrlQuery);
    }

    /**
     * @throws InvalidTokenException
     */
    public function testThatServiceCanUseLaravelCache()
    {
        $cache_key = md5('https://__invalid_domain__/.well-known/jwks.json');
        cache([$cache_key => [uniqid()]], 10);
        session(['auth0__nonce' => uniqid()]);

        $service = new Auth0Service(['domain' => '__invalid_domain__'] + self::$defaultConfig);

        // Without the cache set above, would expect a cURL error for a bad domain.
        $this->expectException(InvalidTokenException::class);
        $service->decodeJWT(uniqid());
    }

    public function testThatGuardAuthenticatesUsers()
    {
        $this->assertTrue(\Auth('auth0')->guest());

        $user = new Auth0JWTUser(['sub' => 'x']);

        \Auth('auth0')->setUser($user);

        $this->assertTrue(\Auth('auth0')->check());
    }

    /*
     * Test suite helpers
     */

    protected function getPackageProviders($app)
    {
        return [Auth0ServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Auth0' => Auth0Facade::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('auth.guards.auth0', ['driver' => 'auth0', 'provider' => 'auth0']);
        $app['config']->set('auth.providers.auth0', ['driver' => 'auth0']);
        $app['config']->set('laravel-auth0', self::$defaultConfig);
    }
}
