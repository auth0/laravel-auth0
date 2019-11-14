<?php
namespace Auth0\Login\Tests;

use Auth0\Login\Auth0Service;
use Auth0\Login\Facade\Auth0 as Auth0Facade;
use Auth0\Login\LoginServiceProvider as Auth0ServiceProvider;
use Auth0\SDK\API\Helpers\State\DummyStateHandler;
use Auth0\SDK\Store\EmptyStore;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Session;

class Auth0ServiceTest extends OrchestraTestCase
{
    public static $defaultConfig;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$defaultConfig = [
            'domain' => 'test.auth0.com',
            'client_id' => '__test_client_id__',
            'client_secret' => '__test_client_secret__',
            'redirect_uri' => 'https://example.com/callback',
        ];
    }

    public function testThatServiceUsesSessionStoreByDefault()
    {
        Session::put('auth0__user', '__test_user__');
        $service = new Auth0Service(self::$defaultConfig);
        $user = $service->getUser();

        $this->assertArrayHasKey('profile', $user);
        $this->assertEquals('__test_user__', $user['profile']);
    }

    public function testThatServiceSetsEmptyStoreFromConfigAndConstructor()
    {
        Session::put('auth0__user', '__test_user__');

        $service = new Auth0Service(self::$defaultConfig + ['store' => false, 'state_handler' => false]);
        $this->assertNull($service->getUser());

        $service = new Auth0Service(self::$defaultConfig, new EmptyStore(), new DummyStateHandler());
        $this->assertNull($service->getUser());

        $service = new Auth0Service(self::$defaultConfig);
        $this->assertIsArray($service->getUser());
    }

    public function testThatServiceLoginReturnsRedirect()
    {

        $service = new Auth0Service(self::$defaultConfig);
        $redirect = $service->login();

        $this->assertInstanceOf( \Illuminate\Http\RedirectResponse::class, $redirect );

        $targetUrl = parse_url($redirect->getTargetUrl());

        $this->assertEquals('test.auth0.com', $targetUrl['host']);

        $targetUrlQuery = explode('&', $targetUrl['query']);

        $this->assertContains('redirect_uri=https%3A%2F%2Fexample.com%2Fcallback', $targetUrlQuery);
        $this->assertContains('client_id=__test_client_id__', $targetUrlQuery);
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
}
