<?php

declare(strict_types=1);

use Auth0\Laravel\Entities\Credential;
use Auth0\Laravel\Model\Stateless\User;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;

uses()->group('stateful', 'model', 'model.credential');

beforeEach(function (): void {
    $this->laravel = app('auth0');
    $this->guard = auth('testGuard');
    $this->sdk = $this->laravel->getSdk();
    $this->config = $this->sdk->configuration();
    $this->session = $this->config->getSessionStorage();
    $this->transient = $this->config->getTransientStorage();
    $this->secret = uniqid();

    $this->config->setDomain('my-domain.auth0.com');
    $this->config->setClientId('my_client_id');
    $this->config->setClientSecret($this->secret);
    $this->config->setCookieSecret('my_cookie_secret');
    $this->config->setTokenAlgorithm(Token::ALGO_HS256);
    $this->config->setStrategy(SdkConfiguration::STRATEGY_API);

    $this->user = new User(['sub' => uniqid('auth0|')]);
    $this->idToken = uniqid();
    $this->accessToken = uniqid();
    $this->accessTokenScope = ['openid', 'profile', 'email', uniqid()];
    $this->accessTokenExpiration = time() + 3600;
    $this->refreshToken = uniqid();
});

test('create() returns a properly configured instance', function (): void {
    $credential = Credential::create(
        user: $this->user,
        idToken: $this->idToken,
        accessToken: $this->accessToken,
        accessTokenScope: $this->accessTokenScope,
        accessTokenExpiration: $this->accessTokenExpiration,
        refreshToken: $this->refreshToken
    );

    expect($credential)
        ->toBeInstanceOf(Credential::class)
        ->getUser()->toBe($this->user)
        ->getIdToken()->toBe($this->idToken)
        ->getAccessToken()->toBe($this->accessToken)
        ->getAccessTokenScope()->toBe($this->accessTokenScope)
        ->getAccessTokenExpiration()->toBe($this->accessTokenExpiration)
        ->getRefreshToken()->toBe($this->refreshToken);
});

it('clear() nullifies all properties', function (): void {
    $credential = Credential::create(
        user: $this->user,
        idToken: $this->idToken,
        accessToken: $this->accessToken,
        accessTokenScope: $this->accessTokenScope,
        accessTokenExpiration: $this->accessTokenExpiration,
        refreshToken: $this->refreshToken,
    );

    expect($credential)
        ->toBeInstanceOf(Credential::class)
        ->getUser()->toBe($this->user)
        ->getIdToken()->toBe($this->idToken)
        ->getAccessToken()->toBe($this->accessToken)
        ->getAccessTokenScope()->toBe($this->accessTokenScope)
        ->getAccessTokenExpiration()->toBe($this->accessTokenExpiration)
        ->getRefreshToken()->toBe($this->refreshToken);

    expect($credential->clear())
        ->toBeInstanceOf(Credential::class)
        ->getUser()->toBeNull()
        ->getIdToken()->toBeNull()
        ->getAccessToken()->toBeNull()
        ->getAccessTokenScope()->toBeNull()
        ->getAccessTokenExpiration()->toBeNull()
        ->getRefreshToken()->toBeNull();
});

it('setUser() assigns a correct value', function (): void {
    $credential = Credential::create();

    expect($credential)
        ->toBeInstanceOf(Credential::class)
        ->getUser()->toBeNull();

    expect($credential->setUser($this->user))
        ->toBeInstanceOf(Credential::class)
        ->getUser()->toBe($this->user);
});

it('setIdToken() assigns a correct value', function (): void {
    $credential = Credential::create();

    expect($credential)
        ->toBeInstanceOf(Credential::class)
        ->getIdToken()->toBeNull();

    expect($credential->setIdToken($this->idToken))
        ->toBeInstanceOf(Credential::class)
        ->getIdToken()->toBe($this->idToken);
});

it('setAccessToken() assigns a correct value', function (): void {
    $credential = Credential::create();

    expect($credential)
        ->toBeInstanceOf(Credential::class)
        ->getAccessToken()->toBeNull();

    expect($credential->setAccessToken($this->accessToken))
        ->toBeInstanceOf(Credential::class)
        ->getAccessToken()->toBe($this->accessToken);
});

it('setAccessTokenScope() assigns a correct value', function (): void {
    $credential = Credential::create();

    expect($credential)
        ->toBeInstanceOf(Credential::class)
        ->getAccessTokenScope()->toBeNull();

    expect($credential->setAccessTokenScope($this->accessTokenScope))
        ->toBeInstanceOf(Credential::class)
        ->getAccessTokenScope()->toBe($this->accessTokenScope);
});

it('setAccessTokenExpiration() assigns a correct value', function (): void {
    $credential = Credential::create();

    expect($credential)
        ->toBeInstanceOf(Credential::class)
        ->getAccessTokenExpiration()->toBeNull();

    expect($credential->setAccessTokenExpiration($this->accessTokenExpiration))
        ->toBeInstanceOf(Credential::class)
        ->getAccessTokenExpiration()->toBe($this->accessTokenExpiration);
});

it('setRefreshToken() assigns a correct value', function (): void {
    $credential = Credential::create();

    expect($credential)
        ->toBeInstanceOf(Credential::class)
        ->getRefreshToken()->toBeNull();

    expect($credential->setRefreshToken($this->refreshToken))
        ->toBeInstanceOf(Credential::class)
        ->getRefreshToken()->toBe($this->refreshToken);
});

it('getAccessTokenExpired() returns a correct value', function (): void {
    $credential = Credential::create();

    expect($credential)
        ->toBeInstanceOf(Credential::class)
        ->getAccessTokenExpired()->toBeNull();

    expect($credential->setAccessTokenExpiration($this->accessTokenExpiration))
        ->toBeInstanceOf(Credential::class)
        ->getAccessTokenExpired()->toBeFalse();

    expect($credential->setAccessTokenExpiration($this->accessTokenExpiration - 3600 * 2))
        ->toBeInstanceOf(Credential::class)
        ->getAccessTokenExpired()->toBeTrue();
});

it('jsonSerialize() returns a correct structure', function (): void {
    $credential = Credential::create(
        user: $this->user,
        idToken: $this->idToken,
        accessToken: $this->accessToken,
        accessTokenScope: $this->accessTokenScope,
        accessTokenExpiration: $this->accessTokenExpiration,
        refreshToken: $this->refreshToken,
    );

    expect(json_encode($credential))
        ->json()
            ->user->toBe(json_encode($this->user))
            ->idToken->toBe($this->idToken)
            ->accessToken->toBe($this->accessToken)
            ->accessTokenScope->toBe($this->accessTokenScope)
            ->accessTokenExpiration->toBe($this->accessTokenExpiration)
            ->refreshToken->toBe($this->refreshToken);
});
