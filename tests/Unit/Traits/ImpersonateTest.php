<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Auth\Guards\SessionGuard;
use Auth0\Laravel\Auth\Guards\TokenGuard;
use Auth0\Laravel\Entities\Credential;
use Auth0\Laravel\Traits\Impersonate;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use PsrMock\Psr18\Client as MockHttpClient;
use PsrMock\Psr17\RequestFactory as MockRequestFactory;
use PsrMock\Psr17\ResponseFactory as MockResponseFactory;
use PsrMock\Psr17\StreamFactory as MockStreamFactory;

uses()->group('trait', 'impersonate');

uses(Impersonate::class);

beforeEach(function (): void {
    $this->secret = uniqid();

    config([
        'auth0.strategy' => SdkConfiguration::STRATEGY_API,
        'auth0.domain' => uniqid() . '.auth0.com',
        'auth0.clientId' => uniqid(),
        'auth0.audience' => [uniqid()],
        'auth0.clientSecret' => $this->secret,
        'auth0.cookieSecret' => uniqid(),
        'auth0.tokenAlgorithm' => Token::ALGO_HS256,
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();
});

it('impersonates with other guards', function (): void {
    config([
        'auth.defaults.guard' => 'web',
        'auth.guards.legacyGuard' => null
    ]);

    $route = '/' . uniqid();

    Route::middleware('auth0.authorize')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = Credential::create(
        user: new \Auth0\Laravel\Model\Stateless\User(['sub' => uniqid()]),
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->impersonate($imposter)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK);

    expect($this->guard)
        ->user()->toBeNull();
});

it('impersonates a user against auth0.authenticate', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = Credential::create(
        user: new \Auth0\Laravel\Model\Stateful\User(['sub' => uniqid()]),
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->impersonate($imposter, Guard::SOURCE_SESSION)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($imposter->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(\Auth0\Laravel\Model\Stateful\User::class)
        ->toBe($imposter->getUser());
});

it('impersonates a user against auth0.authenticate.optional', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate.optional')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = Credential::create(
        user: new \Auth0\Laravel\Model\Stateful\User(['sub' => uniqid()]),
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->impersonate($imposter, Guard::SOURCE_SESSION)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($imposter->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(\Auth0\Laravel\Model\Stateful\User::class)
        ->toBe($imposter->getUser());
});

it('impersonates a user against auth0.authenticate using a scope', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate:read:messages')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = Credential::create(
        user: new \Auth0\Laravel\Model\Stateful\User(['sub' => uniqid()]),
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->impersonate($imposter, Guard::SOURCE_SESSION)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($imposter->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(\Auth0\Laravel\Model\Stateful\User::class)
        ->toBe($imposter->getUser());
});

it('impersonates a user against auth0.authorize', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = Credential::create(
        user: new \Auth0\Laravel\Model\Stateless\User(['sub' => uniqid()]),
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->impersonate($imposter, Guard::SOURCE_TOKEN)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($imposter->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(\Auth0\Laravel\Model\Stateless\User::class)
        ->toBe($imposter->getUser());
});

it('impersonates a user against auth0.authorize.optional', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize.optional')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = Credential::create(
        user: new \Auth0\Laravel\Model\Stateless\User(['sub' => uniqid()]),
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->impersonate($imposter, Guard::SOURCE_TOKEN)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($imposter->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(\Auth0\Laravel\Model\Stateless\User::class)
        ->toBe($imposter->getUser());
});

it('impersonates a user against auth0.authorize using a scope', function (): void {
    $route = '/' . uniqid();

    $imposter = Credential::create(
        user: new \Auth0\Laravel\Model\Stateless\User(['sub' => uniqid()]),
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    Route::middleware('auth0.authorize:read:messages')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $this->impersonate($imposter, Guard::SOURCE_TOKEN)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($imposter->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(\Auth0\Laravel\Model\Stateless\User::class)
        ->toBe($imposter->getUser());
});

it('SessionGuard returns the impersonated user', function (): void {
    config([
        'auth.defaults.guard' => 'sessionGuard',
    ]);

    $route = '/' . uniqid();

    Route::get($route, function () use ($route): string {
        return json_encode(['route' => get_class(auth()->guard()), 'user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = new \Auth0\Laravel\Model\Stateful\User(['sub' => uniqid()]);

    $credential = Credential::create(
        user: $imposter,
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600,
        refreshToken: uniqid(),
    );

    $response = $this->impersonate($credential)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK);

    expect($response->json())
        ->route->toBe(SessionGuard::class)
        ->user->json()->sub->toBe($imposter->getAuthIdentifier());

    expect(auth('legacyGuard'))
        ->user()->toBeNull();

    expect(auth('tokenGuard'))
        ->user()->toBeNull();

    expect(auth('sessionGuard'))
        ->isImpersonating()->toBeTrue()
        ->user()->toEqual($imposter)
        ->find()->toEqual($credential)
        ->findSession()->toEqual($credential)
        ->getCredential()->toEqual($credential);

    $client = new MockHttpClient(requestLimit: 0);
    $this->sdk->configuration()->setHttpClient($client);

    expect(auth('sessionGuard'))
        ->refreshUser();

    auth('sessionGuard')->setUser(new \Auth0\Laravel\Model\Stateful\User(['sub' => uniqid()]));

    expect(auth('sessionGuard'))
        ->isImpersonating()->toBeFalse()
        ->user()->not()->toEqual($imposter)
        ->find()->not()->toEqual($credential)
        ->findSession()->not()->toEqual($credential)
        ->getCredential()->not()->toEqual($credential);
});

it('TokenGuard returns the impersonated user', function (): void {
    config([
        'auth.defaults.guard' => 'tokenGuard',
    ]);

    $route = '/' . uniqid();

    Route::get($route, function () use ($route): string {
        return json_encode(['route' => get_class(auth()->guard()), 'user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = new \Auth0\Laravel\Model\Stateless\User(['sub' => uniqid()]);

    $credential = Credential::create(
        user: $imposter,
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600,
        refreshToken: uniqid(),
    );

    $response = $this->impersonate($credential)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK);

    expect($response->json())
        ->route->toBe(TokenGuard::class)
        ->user->json()->sub->toBe($imposter->getAuthIdentifier());

    expect(auth('legacyGuard'))
        ->user()->toBeNull();

    expect(auth('sessionGuard'))
        ->user()->toBeNull();

    expect(auth('tokenGuard'))
        ->isImpersonating()->toBeTrue()
        ->getImposterSource()->toBe(Guard::SOURCE_TOKEN)
        ->user()->toEqual($imposter)
        ->find()->toEqual($credential)
        ->findToken()->toEqual($credential)
        ->getCredential()->toEqual($credential);

    $client = new MockHttpClient(requestLimit: 0);
    $this->sdk->configuration()->setHttpClient($client);

    expect(auth('tokenGuard'))
        ->refreshUser();

    auth('tokenGuard')->setUser(new \Auth0\Laravel\Model\Stateless\User(['sub' => uniqid()]));

    expect(auth('tokenGuard'))
        ->isImpersonating()->toBeFalse()
        ->user()->not()->toEqual($imposter)
        ->find()->not()->toEqual($credential)
        ->findToken()->not()->toEqual($credential)
        ->getCredential()->not()->toEqual($credential);
});

it('Guard returns the impersonated user', function (): void {
    config([
        'auth.defaults.guard' => 'legacyGuard',
    ]);

    $route = '/' . uniqid();

    Route::get($route, function () use ($route): string {
        return json_encode(['route' => get_class(auth()->guard()), 'user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = new \Auth0\Laravel\Model\Stateful\User(['sub' => uniqid()]);

    $credential = Credential::create(
        user: $imposter,
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600,
        refreshToken: uniqid(),
    );

    $response = $this->impersonate(credential: $credential, source: Guard::SOURCE_SESSION)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK);

    expect($response->json())
        ->route->toBe(Guard::class)
        ->user->json()->sub->toBe($imposter->getAuthIdentifier());

    expect(auth('tokenGuard'))
        ->user()->toBeNull();

    expect(auth('sessionGuard'))
        ->user()->toBeNull();

    expect(auth('legacyGuard'))
        ->isImpersonating()->toBeTrue()
        ->getImposterSource()->toBe(Guard::SOURCE_SESSION)
        ->user()->toEqual($imposter)
        ->find()->toEqual($credential)
        ->getCredential()->toEqual($credential);

    $client = new MockHttpClient(requestLimit: 0);
    $this->sdk->configuration()->setHttpClient($client);

    auth('legacyGuard')->refreshUser();
    auth('legacyGuard')->setUser(new \Auth0\Laravel\Model\Stateful\User(['sub' => uniqid()]));

    expect(auth('legacyGuard'))
        ->isImpersonating()->toBeFalse()
        ->user()->not()->toEqual($imposter)
        ->find()->not()->toEqual($credential)
        ->getCredential()->not()->toEqual($credential);
});

it('Guard clears the impersonated user during logout()', function (): void {
    config([
        'auth.defaults.guard' => 'legacyGuard',
    ]);

    $route = '/' . uniqid();

    Route::get($route, function () use ($route): string {
        return json_encode(['route' => get_class(auth()->guard()), 'user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = new \Auth0\Laravel\Model\Stateful\User(['sub' => uniqid()]);

    $credential = Credential::create(
        user: $imposter,
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600,
        refreshToken: uniqid(),
    );

    $response = $this->impersonate(credential: $credential, source: Guard::SOURCE_TOKEN)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK);

    expect($response->json())
        ->route->toBe(Guard::class)
        ->user->json()->sub->toBe($imposter->getAuthIdentifier());

    expect(auth('legacyGuard'))
        ->isImpersonating()->toBeTrue()
        ->getImposterSource()->toBe(Guard::SOURCE_TOKEN)
        ->user()->toEqual($imposter)
        ->find()->toEqual($credential)
        ->getCredential()->toEqual($credential);

    auth('legacyGuard')->logout();

    expect(auth('legacyGuard'))
        ->isImpersonating()->toBeFalse()
        ->user()->toBeNull()
        ->find()->toBeNull()
        ->getCredential()->toBeNull();
});
