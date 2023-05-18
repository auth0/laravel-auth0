<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Guards\AuthenticationGuard;
use Auth0\Laravel\Guards\AuthorizationGuard;
use Auth0\Laravel\Entities\CredentialEntity;
use Auth0\Laravel\Traits\Impersonate;
use Auth0\Laravel\Users\ImposterUser;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use PsrMock\Psr18\Client as MockHttpClient;

uses()->group('trait', 'impersonate');

uses(Impersonate::class);

beforeEach(function (): void {
    $this->secret = uniqid();

    config([
        'auth0.AUTH0_CONFIG_VERSION' => 2,
        'auth0.guards.default.strategy' => SdkConfiguration::STRATEGY_API,
        'auth0.guards.default.domain' => uniqid() . '.auth0.com',
        'auth0.guards.default.clientId' => uniqid(),
        'auth0.guards.default.audience' => [uniqid()],
        'auth0.guards.default.clientSecret' => $this->secret,
        'auth0.guards.default.cookieSecret' => uniqid(),
        'auth0.guards.default.tokenAlgorithm' => Token::ALGO_HS256,
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

    $imposter = CredentialEntity::create(
        user: new ImposterUser(['sub' => uniqid()]),
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
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

    $imposter = CredentialEntity::create(
        user: new ImposterUser(['sub' => uniqid()]),
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->impersonate($imposter, Guard::SOURCE_SESSION)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($imposter->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(ImposterUser::class)
        ->toBe($imposter->getUser());
});

it('impersonates a user against auth0.authenticate.optional', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate.optional')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = CredentialEntity::create(
        user: new ImposterUser(['sub' => uniqid()]),
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->impersonate($imposter, Guard::SOURCE_SESSION)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($imposter->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(ImposterUser::class)
        ->toBe($imposter->getUser());
});

it('impersonates a user against auth0.authenticate using a scope', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authenticate:read:messages')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = CredentialEntity::create(
        user: new ImposterUser(['sub' => uniqid()]),
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->impersonate($imposter, Guard::SOURCE_SESSION)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($imposter->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(ImposterUser::class)
        ->toBe($imposter->getUser());
});

it('impersonates a user against auth0.authorize', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = CredentialEntity::create(
        user: new ImposterUser(['sub' => uniqid()]),
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->impersonate($imposter, Guard::SOURCE_TOKEN)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($imposter->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(ImposterUser::class)
        ->toBe($imposter->getUser());
});

it('impersonates a user against auth0.authorize.optional', function (): void {
    $route = '/' . uniqid();

    Route::middleware('auth0.authorize.optional')->get($route, function () use ($route): string {
        return json_encode(['user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = CredentialEntity::create(
        user: new ImposterUser(['sub' => uniqid()]),
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->impersonate($imposter, Guard::SOURCE_TOKEN)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK)
         ->assertJson(['status' => $route])
         ->assertJson(['user' => json_encode($imposter->getUser())]);

    expect($this->guard)
        ->user()->toBeInstanceOf(ImposterUser::class)
        ->toBe($imposter->getUser());
});

it('impersonates a user against auth0.authorize using a scope', function (): void {
    $route = '/' . uniqid();

    $imposter = CredentialEntity::create(
        user: new ImposterUser(['sub' => uniqid()]),
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
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
        ->user()->toBeInstanceOf(ImposterUser::class)
        ->toBe($imposter->getUser());
});

it('AuthenticationGuard returns the impersonated user', function (): void {
    config([
        'auth.defaults.guard' => 'auth0-session',
    ]);

    $route = '/' . uniqid();

    Route::get($route, function () use ($route): string {
        return json_encode(['route' => get_class(auth()->guard()), 'user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = new ImposterUser(['sub' => uniqid()]);

    $credential = CredentialEntity::create(
        user: $imposter,
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600,
        refreshToken: uniqid(),
    );

    $response = $this->impersonate($credential)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK);

    expect($response->json())
        ->route->toBe(AuthenticationGuard::class)
        ->user->json()->sub->toBe($imposter->getAuthIdentifier());

    expect(auth('legacyGuard'))
        ->user()->toBeNull();

    expect(auth('auth0-api'))
        ->user()->toBeNull();

    expect(auth('auth0-session'))
        ->isImpersonating()->toBeTrue()
        ->user()->toEqual($imposter)
        ->find()->toEqual($credential)
        ->findSession()->toEqual($credential)
        ->getCredential()->toEqual($credential);

    $client = new MockHttpClient(requestLimit: 0);
    $this->sdk->configuration()->setHttpClient($client);

    expect(auth('auth0-session'))
        ->refreshUser();

    auth('auth0-session')->setUser(new ImposterUser(['sub' => uniqid()]));

    expect(auth('auth0-session'))
        ->isImpersonating()->toBeFalse()
        ->user()->not()->toEqual($imposter)
        ->find()->not()->toEqual($credential)
        ->findSession()->not()->toEqual($credential)
        ->getCredential()->not()->toEqual($credential);
});

it('AuthorizationGuard returns the impersonated user', function (): void {
    config([
        'auth.defaults.guard' => 'auth0-api',
    ]);

    $route = '/' . uniqid();

    Route::get($route, function () use ($route): string {
        return json_encode(['route' => get_class(auth()->guard()), 'user' => json_encode(auth()->user()), 'status' => $route]);
    });

    $imposter = new ImposterUser(['sub' => uniqid()]);

    $credential = CredentialEntity::create(
        user: $imposter,
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600,
        refreshToken: uniqid(),
    );

    $response = $this->impersonate($credential)
         ->getJson($route)
         ->assertStatus(Response::HTTP_OK);

    expect($response->json())
        ->route->toBe(AuthorizationGuard::class)
        ->user->json()->sub->toBe($imposter->getAuthIdentifier());

    expect(auth('legacyGuard'))
        ->user()->toBeNull();

    expect(auth('auth0-session'))
        ->user()->toBeNull();

    expect(auth('auth0-api'))
        ->isImpersonating()->toBeTrue()
        ->getImposterSource()->toBe(Guard::SOURCE_TOKEN)
        ->user()->toEqual($imposter)
        ->find()->toEqual($credential)
        ->findToken()->toEqual($credential)
        ->getCredential()->toEqual($credential);

    $client = new MockHttpClient(requestLimit: 0);
    $this->sdk->configuration()->setHttpClient($client);

    expect(auth('auth0-api'))
        ->refreshUser();

    auth('auth0-api')->setUser(new ImposterUser(['sub' => uniqid()]));

    expect(auth('auth0-api'))
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

    $imposter = new ImposterUser(['sub' => uniqid()]);

    $credential = CredentialEntity::create(
        user: $imposter,
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
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

    expect(auth('auth0-api'))
        ->user()->toBeNull();

    expect(auth('auth0-session'))
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
    auth('legacyGuard')->setUser(new ImposterUser(['sub' => uniqid()]));

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

    $imposter = new ImposterUser(['sub' => uniqid()]);

    $credential = CredentialEntity::create(
        user: $imposter,
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
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
