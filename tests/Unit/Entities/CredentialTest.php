<?php

declare(strict_types=1);

use Auth0\Laravel\Entities\CredentialEntity;
use Auth0\Laravel\Model\Stateless\User;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;

uses()->group('stateful', 'model', 'model.credential');

beforeEach(function (): void {
    $this->secret = uniqid();

    config([
        'auth0.default.strategy' => SdkConfiguration::STRATEGY_API,
        'auth0.default.domain' => uniqid() . '.auth0.com',
        'auth0.default.clientId' => uniqid(),
        'auth0.default.audience' => [uniqid()],
        'auth0.default.clientSecret' => $this->secret,
        'auth0.default.tokenAlgorithm' => Token::ALGO_HS256,
        'auth0.default.routes.home' => '/' . uniqid(),
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();

    $this->user = new User(['sub' => uniqid('auth0|')]);
    $this->idToken = uniqid();
    $this->accessToken = uniqid();
    $this->accessTokenScope = ['openid', 'profile', 'email', uniqid()];
    $this->accessTokenExpiration = time() + 3600;
    $this->refreshToken = uniqid();
});

test('create() returns a properly configured instance', function (): void {
    $credential = CredentialEntity::create(
        user: $this->user,
        idToken: $this->idToken,
        accessToken: $this->accessToken,
        accessTokenScope: $this->accessTokenScope,
        accessTokenExpiration: $this->accessTokenExpiration,
        refreshToken: $this->refreshToken
    );

    expect($credential)
        ->toBeInstanceOf(CredentialEntity::class)
        ->getUser()->toBe($this->user)
        ->getIdToken()->toBe($this->idToken)
        ->getAccessToken()->toBe($this->accessToken)
        ->getAccessTokenScope()->toBe($this->accessTokenScope)
        ->getAccessTokenExpiration()->toBe($this->accessTokenExpiration)
        ->getRefreshToken()->toBe($this->refreshToken);
});

it('clear() nullifies all properties', function (): void {
    $credential = CredentialEntity::create(
        user: $this->user,
        idToken: $this->idToken,
        accessToken: $this->accessToken,
        accessTokenScope: $this->accessTokenScope,
        accessTokenExpiration: $this->accessTokenExpiration,
        refreshToken: $this->refreshToken,
    );

    expect($credential)
        ->toBeInstanceOf(CredentialEntity::class)
        ->getUser()->toBe($this->user)
        ->getIdToken()->toBe($this->idToken)
        ->getAccessToken()->toBe($this->accessToken)
        ->getAccessTokenScope()->toBe($this->accessTokenScope)
        ->getAccessTokenExpiration()->toBe($this->accessTokenExpiration)
        ->getRefreshToken()->toBe($this->refreshToken);

    expect($credential->clear())
        ->toBeInstanceOf(CredentialEntity::class)
        ->getUser()->toBeNull()
        ->getIdToken()->toBeNull()
        ->getAccessToken()->toBeNull()
        ->getAccessTokenScope()->toBeNull()
        ->getAccessTokenExpiration()->toBeNull()
        ->getRefreshToken()->toBeNull();
});

it('setUser() assigns a correct value', function (): void {
    $credential = CredentialEntity::create();

    expect($credential)
        ->toBeInstanceOf(CredentialEntity::class)
        ->getUser()->toBeNull();

    expect($credential->setUser($this->user))
        ->toBeInstanceOf(CredentialEntity::class)
        ->getUser()->toBe($this->user);
});

it('setIdToken() assigns a correct value', function (): void {
    $credential = CredentialEntity::create();

    expect($credential)
        ->toBeInstanceOf(CredentialEntity::class)
        ->getIdToken()->toBeNull();

    expect($credential->setIdToken($this->idToken))
        ->toBeInstanceOf(CredentialEntity::class)
        ->getIdToken()->toBe($this->idToken);
});

it('setAccessToken() assigns a correct value', function (): void {
    $credential = CredentialEntity::create();

    expect($credential)
        ->toBeInstanceOf(CredentialEntity::class)
        ->getAccessToken()->toBeNull();

    expect($credential->setAccessToken($this->accessToken))
        ->toBeInstanceOf(CredentialEntity::class)
        ->getAccessToken()->toBe($this->accessToken);
});

it('setAccessTokenScope() assigns a correct value', function (): void {
    $credential = CredentialEntity::create();

    expect($credential)
        ->toBeInstanceOf(CredentialEntity::class)
        ->getAccessTokenScope()->toBeNull();

    expect($credential->setAccessTokenScope($this->accessTokenScope))
        ->toBeInstanceOf(CredentialEntity::class)
        ->getAccessTokenScope()->toBe($this->accessTokenScope);
});

it('setAccessTokenExpiration() assigns a correct value', function (): void {
    $credential = CredentialEntity::create();

    expect($credential)
        ->toBeInstanceOf(CredentialEntity::class)
        ->getAccessTokenExpiration()->toBeNull();

    expect($credential->setAccessTokenExpiration($this->accessTokenExpiration))
        ->toBeInstanceOf(CredentialEntity::class)
        ->getAccessTokenExpiration()->toBe($this->accessTokenExpiration);
});

it('setRefreshToken() assigns a correct value', function (): void {
    $credential = CredentialEntity::create();

    expect($credential)
        ->toBeInstanceOf(CredentialEntity::class)
        ->getRefreshToken()->toBeNull();

    expect($credential->setRefreshToken($this->refreshToken))
        ->toBeInstanceOf(CredentialEntity::class)
        ->getRefreshToken()->toBe($this->refreshToken);
});

it('getAccessTokenExpired() returns a correct value', function (): void {
    $credential = CredentialEntity::create();

    expect($credential)
        ->toBeInstanceOf(CredentialEntity::class)
        ->getAccessTokenExpired()->toBeNull();

    expect($credential->setAccessTokenExpiration($this->accessTokenExpiration))
        ->toBeInstanceOf(CredentialEntity::class)
        ->getAccessTokenExpired()->toBeFalse();

    expect($credential->setAccessTokenExpiration($this->accessTokenExpiration - 3600 * 2))
        ->toBeInstanceOf(CredentialEntity::class)
        ->getAccessTokenExpired()->toBeTrue();
});

it('jsonSerialize() returns a correct structure', function (): void {
    $credential = CredentialEntity::create(
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
