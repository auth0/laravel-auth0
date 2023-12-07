<?php

declare(strict_types=1);

use Auth0\Laravel\Service;
use Auth0\Laravel\Bridges\{CacheBridge, CacheBridgeContract, SessionBridgeContract};
use Auth0\SDK\Contract\Auth0Interface as SdkContract;
use Auth0\SDK\Auth0 as SDKAuth0;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Contract\API\ManagementInterface;
use Auth0\SDK\Store\MemoryStore;
use Auth0\SDK\Token\Generator;
use Illuminate\Support\Facades\Route;
use Psr\Cache\CacheItemPoolInterface;
use PsrMock\Psr17\ResponseFactory;

uses()->group('Service');

beforeEach(function (): void {
    $this->secret = uniqid();

    config([
        'auth0.AUTH0_CONFIG_VERSION' => 2,
        'auth0.guards.default.strategy' => SdkConfiguration::STRATEGY_REGULAR,
        'auth0.guards.default.domain' => uniqid() . '.auth0.com',
        'auth0.guards.default.clientId' => uniqid(),
        'auth0.guards.default.clientSecret' => $this->secret,
        'auth0.guards.default.cookieSecret' => uniqid(),
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();
    $this->config = $this->sdk->configuration();
    $this->session = $this->config->getSessionStorage();
});

it('returns a Management API class', function (): void {
    expect($this->laravel->management())->toBeInstanceOf(ManagementInterface::class);
});

it('can get/set the configuration', function (): void {
    expect($this->laravel->getConfiguration())->toBeInstanceOf(SdkConfiguration::class);

    $configuration = new SdkConfiguration(['strategy' => 'none', 'domain' => uniqid() . '.auth0.test']);
    $this->laravel->setConfiguration($configuration);
    expect($this->laravel->getConfiguration())->toBe($configuration);

    $domain = uniqid() . '.auth0.test';
    $configuration->setDomain($domain);
    expect($this->laravel->getConfiguration()->getDomain())->toBe($domain);

    $configuration = new SdkConfiguration(['strategy' => 'none', 'domain' => uniqid() . '.auth0.test']);
    $this->laravel->setConfiguration($configuration);
    expect($this->laravel->getConfiguration())->toBe($configuration);

    $sdk = $this->laravel->getSdk();
    $configuration = new SdkConfiguration(['strategy' => 'none', 'domain' => uniqid() . '.auth0.test']);
    $this->laravel->setConfiguration($configuration);
    expect($this->laravel->getConfiguration())->toBe($configuration);
    expect($sdk->configuration())->toBe($configuration);
});

it('can get the sdk credentials', function (): void {
    expect($this->laravel->getCredentials())
        ->toBeNull();

    $this->session->set('user', ['sub' => 'hello|world']);
    $this->session->set('idToken', (string) Generator::create((createRsaKeys())->private));
    $this->session->set('accessToken', (string) Generator::create((createRsaKeys())->private));
    $this->session->set('accessTokenScope', [uniqid()]);
    $this->session->set('accessTokenExpiration', time() - 1000);

    // As we manually set the session values, we need to refresh the SDK state to ensure it's in sync.
    $this->sdk->refreshState();

    expect($this->laravel->getCredentials())
        ->toBeObject()
        ->toHaveProperty('accessToken', $this->session->get('accessToken'))
        ->toHaveProperty('accessTokenScope', $this->session->get('accessTokenScope'))
        ->toHaveProperty('accessTokenExpiration', $this->session->get('accessTokenExpiration'))
        ->toHaveProperty('idToken', $this->session->get('idToken'))
        ->toHaveProperty('user', $this->session->get('user'));
});

it('can get/set the SDK', function (): void {
    expect($this->laravel->getSdk())->toBeInstanceOf(SdkContract::class);

    $sdk = new SDKAuth0(['strategy' => 'none']);
    $this->laravel->setSdk($sdk);
    expect($this->laravel->getSdk())->toBeInstanceOf(SdkContract::class);
});

it('can reset the internal static state', function (): void {
    $cache = spl_object_id($this->laravel->getSdk());

    unset($this->laravel); // Force the object to be destroyed. Static state will remain.

    $laravel = app('auth0');
    $updated = spl_object_id($laravel->getSdk());
    expect($cache)->toBe($updated);

    $laravel->reset(); // Reset the static state.

    $laravel = app('auth0');
    $updated = spl_object_id($laravel->getSdk());
    expect($cache)->not->toBe($updated);
});

test('bootStrategy() rejects non-string values', function (): void {
    $method = new ReflectionMethod(Service::class, 'bootStrategy');
    $method->setAccessible(true);

    expect($method->invoke($this->laravel, ['strategy' => 123]))
        ->toMatchArray(['strategy' => SdkConfiguration::STRATEGY_REGULAR]);
});

test('bootSessionStorage() behaves as expected', function (): void {
    $method = new ReflectionMethod(Service::class, 'bootSessionStorage');
    $method->setAccessible(true);

    expect($method->invoke($this->laravel, []))
        ->sessionStorage->toBeInstanceOf(SessionBridgeContract::class);

    expect($method->invoke($this->laravel, ['sessionStorage' => null]))
        ->sessionStorage->toBeInstanceOf(SessionBridgeContract::class);

    expect($method->invoke($this->laravel, ['sessionStorage' => false]))
        ->sessionStorage->toBeNull();

    expect($method->invoke($this->laravel, ['sessionStorage' => CacheBridge::class]))
        ->sessionStorage->toBeNull();

    expect($method->invoke($this->laravel, ['sessionStorage' => MemoryStore::class]))
        ->sessionStorage->toBeInstanceOf(MemoryStore::class);

    $this->app->singleton('testStore', static fn (): MemoryStore => app(MemoryStore::class));

    expect($method->invoke($this->laravel, ['sessionStorage' => 'testStore']))
        ->sessionStorage->toBeInstanceOf(MemoryStore::class);
});

test('bootTransientStorage() behaves as expected', function (): void {
    $method = new ReflectionMethod(Service::class, 'bootTransientStorage');
    $method->setAccessible(true);

    expect($method->invoke($this->laravel, []))
        ->transientStorage->toBeInstanceOf(SessionBridgeContract::class);

    expect($method->invoke($this->laravel, ['transientStorage' => null]))
        ->transientStorage->toBeInstanceOf(SessionBridgeContract::class);

    expect($method->invoke($this->laravel, ['transientStorage' => false]))
        ->transientStorage->toBeNull();

    expect($method->invoke($this->laravel, ['transientStorage' => CacheBridge::class]))
        ->transientStorage->toBeNull();

    expect($method->invoke($this->laravel, ['transientStorage' => MemoryStore::class]))
        ->transientStorage->toBeInstanceOf(MemoryStore::class);

    $this->app->singleton('testStore', static fn (): MemoryStore => app(MemoryStore::class));

    expect($method->invoke($this->laravel, ['transientStorage' => 'testStore']))
        ->transientStorage->toBeInstanceOf(MemoryStore::class);
});

test('bootTokenCache() behaves as expected', function (): void {
    $method = new ReflectionMethod(Service::class, 'bootTokenCache');
    $method->setAccessible(true);

    expect($method->invoke($this->laravel, []))
        ->tokenCache->toBeInstanceOf(CacheBridgeContract::class);

    expect($method->invoke($this->laravel, ['tokenCache' => null]))
        ->tokenCache->toBeInstanceOf(CacheBridgeContract::class);

    expect($method->invoke($this->laravel, ['tokenCache' => CacheBridge::class]))
        ->tokenCache->toBeInstanceOf(CacheBridgeContract::class);

    expect($method->invoke($this->laravel, ['tokenCache' => false]))
        ->tokenCache->toBeNull();

    expect($method->invoke($this->laravel, ['tokenCache' => MemoryStore::class]))
        ->tokenCache->toBeNull();

    expect($method->invoke($this->laravel, ['tokenCache' => 'cache.psr6']))
        ->tokenCache->toBeInstanceOf(CacheItemPoolInterface::class);
});

test('bootBackchannelLogoutCache() behaves as expected', function (): void {
    $method = new ReflectionMethod(Service::class, 'bootBackchannelLogoutCache');
    $method->setAccessible(true);

    expect($method->invoke($this->laravel, []))
        ->backchannelLogoutCache->toBeInstanceOf(CacheBridgeContract::class);

    expect($method->invoke($this->laravel, ['backchannelLogoutCache' => null]))
        ->backchannelLogoutCache->toBeInstanceOf(CacheBridgeContract::class);

    expect($method->invoke($this->laravel, ['backchannelLogoutCache' => CacheBridge::class]))
        ->backchannelLogoutCache->toBeInstanceOf(CacheBridgeContract::class);

    expect($method->invoke($this->laravel, ['backchannelLogoutCache' => false]))
        ->backchannelLogoutCache->toBeNull();

    expect($method->invoke($this->laravel, ['backchannelLogoutCache' => MemoryStore::class]))
        ->backchannelLogoutCache->toBeNull();

    expect($method->invoke($this->laravel, ['backchannelLogoutCache' => 'cache.psr6']))
        ->backchannelLogoutCache->toBeInstanceOf(CacheItemPoolInterface::class);
});

// test('bootManagementTokenCache() behaves as expected', function (): void {
//     $method = new ReflectionMethod(Service::class, 'bootManagementTokenCache');
//     $method->setAccessible(true);

//     expect($method->invoke($this->laravel, []))
//         ->managementTokenCache->toBeInstanceOf(CacheBridgeContract::class);

//     expect($method->invoke($this->laravel, ['managementTokenCache' => null]))
//         ->managementTokenCache->toBeInstanceOf(CacheBridgeContract::class);

//     expect($method->invoke($this->laravel, ['managementTokenCache' => CacheBridgeContract::class]))
//         ->managementTokenCache->toBeInstanceOf(CacheBridgeContract::class);

//     expect($method->invoke($this->laravel, ['managementTokenCache' => false]))
//         ->managementTokenCache->toBeNull();

//     expect($method->invoke($this->laravel, ['managementTokenCache' => MemoryStore::class]))
//         ->managementTokenCache->toBeNull();

//     expect($method->invoke($this->laravel, ['managementTokenCache' => 'cache.psr6']))
//         ->managementTokenCache->toBeInstanceOf(CacheItemPoolInterface::class);
// });

test('json() behaves as expected', function (): void {
    $factory = new ResponseFactory;

    $response = $factory->createResponse(200);
    $response->getBody()->write('{"foo":"bar"}');

    expect(Service::json($response))
        ->toBe(['foo' => 'bar']);

    $response = $factory->createResponse(500);
    $response->getBody()->write('{"foo":"bar"}');

    expect(Service::json($response))
        ->toBeNull();

    $response = $factory->createResponse(200);
    $response->getBody()->write(json_encode(true));

    expect(Service::json($response))
        ->toBeNull();
});

test('routes() behaves as expected', function (): void {
    Service::routes();

    expect((array) Route::getRoutes()->get('GET'))
        ->toHaveKeys(['login', 'logout', 'callback']);
});
