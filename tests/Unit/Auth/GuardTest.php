<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Exceptions\AuthenticationException;
use Auth0\Laravel\Entities\CredentialEntity;
use Auth0\Laravel\Users\StatefulUser;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Contract\API\ManagementInterface;
use Auth0\SDK\Exception\ConfigurationException;
use Auth0\SDK\Token;
use Illuminate\Support\Facades\Route;
use PsrMock\Psr18\Client as MockHttpClient;
use PsrMock\Psr17\RequestFactory as MockRequestFactory;
use PsrMock\Psr17\ResponseFactory as MockResponseFactory;
use PsrMock\Psr17\StreamFactory as MockStreamFactory;

uses()->group('auth', 'auth.guard', 'auth.guard.shared');

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

    $this->user = new StatefulUser(['sub' => uniqid('auth0|')]);

    Route::middleware('auth:auth0')->get('/test', function () {
        return 'OK';
    });
});

it('returns its configured name', function (): void {
    expect($this->guard)
        ->toBeInstanceOf(Guard::class)
        ->getName()->toBe('legacyGuard');
});

it('assigns a user at login', function (): void {
    expect($this->guard)
        ->toBeInstanceOf(Guard::class)
        ->user()->toBeNull();

    $this->guard->login(CredentialEntity::create(
        user: $this->user
    ));

    expect($this->guard)
        ->user()->toBe($this->user);

    expect($this->guard)
        ->id()->toBe($this->user->getAuthIdentifier());
});

it('logs out a user', function (): void {
    expect($this->guard)
        ->toBeInstanceOf(Guard::class)
        ->user()->toBeNull();

    $this->guard->login(CredentialEntity::create(
        user: $this->user
    ));

    expect($this->guard)
        ->user()->toBe($this->user);

    $this->guard->logout();

    expect($this->guard)
        ->user()->toBeNull();

    expect($this->guard)
        ->id()->toBeNull();
});

it('forgets a user', function (): void {
    expect($this->guard)
        ->toBeInstanceOf(Guard::class)
        ->user()->toBeNull();

    $this->guard->login(CredentialEntity::create(
        user: $this->user
    ));

    expect($this->guard)
        ->user()->toBe($this->user);

    $this->guard->forgetUser();

    expect($this->guard)
        ->user()->toBeNull();

    expect($this->guard)
        ->id()->toBeNull();
});

it('checks if a user is logged in', function (): void {
    expect($this->guard)
        ->check()->toBeFalse();

    $this->guard->login(CredentialEntity::create(
        user: $this->user
    ));

    expect($this->guard)
        ->check()->toBeTrue();
});

it('checks if a user is a guest', function (): void {
    expect($this->guard)
        ->guest()->toBeTrue();

    $this->guard->login(CredentialEntity::create(
        user: $this->user
    ));

    expect($this->guard)
        ->guest()->toBeFalse();
});

it('gets the user identifier', function (): void {
    $this->guard->login(CredentialEntity::create(
        user: $this->user
    ));

    expect($this->guard)
        ->id()->toBe($this->user->getAuthIdentifier());
});

it('validates a user', function (): void {
    $this->guard->login(CredentialEntity::create(
        user: $this->user
    ));

    expect($this->guard)
        ->validate(['id' => '123'])->toBeFalse()
        ->validate(['id' => '456'])->toBeFalse();
});

it('gets/sets a user', function (): void {
    $this->guard->setUser($this->user);

    expect($this->guard)
        ->user()->toBe($this->user);
});

it('has a user', function (): void {
    $this->guard->setUser($this->user);

    expect($this->guard)
        ->hasUser()->toBeTrue();

    $this->guard->logout();

    expect($this->guard)
        ->hasUser()->toBeFalse();
});

it('clears an imposter at logout', function (): void {
    $this->guard->setImpersonating(CredentialEntity::create(
        user: $this->user
    ));

    expect($this->guard)
        ->hasUser()->toBeTrue()
        ->isImpersonating()->toBeTrue();

    $this->guard->logout();

    expect($this->guard)
        ->isImpersonating()->toBeFalse()
        ->hasUser()->toBeFalse();
});

it('has a scope', function (): void {
    $this->user = new StatefulUser(['sub' => uniqid('auth0|'), 'scope' => 'read:users 456']);

    $credential = CredentialEntity::create(
        user: $this->user,
        accessTokenScope: ['read:users', '456']
    );

    expect($this->guard)
        ->hasScope('read:users', $credential)->toBeTrue()
        ->hasScope('123', $credential)->toBeFalse()
        ->hasScope('456', $credential)->toBeTrue()
        ->hasScope('789', $credential)->toBeFalse()
        ->hasScope('*', $credential)->toBeTrue();

    $credential = CredentialEntity::create(
        user: $this->user
    );

    expect($this->guard)
        ->hasScope('read:users', $credential)->toBeFalse()
        ->hasScope('*', $credential)->toBeTrue();
});

it('checks if a user was authenticated via remember', function (): void {
    $this->guard->login(CredentialEntity::create(
        user: $this->user
    ));

    expect($this->guard)
        ->viaRemember()->toBeFalse();
});

it('returns null if authenticate() is called without being authenticated', function (): void {
    $response = $this->guard->authenticate();
    expect($response)->toBeNull();
})->throws(AuthenticationException::class, AuthenticationException::UNAUTHENTICATED);

it('returns a user from authenticate() if called while authenticated', function (): void {
    $this->guard->login(CredentialEntity::create(
        user: $this->user
    ));

    $response = $this->guard->authenticate();

    expect($response)
        ->toBe($this->user);
});

it('gets/sets a credentials', function (): void {
    $credential = CredentialEntity::create(
        user: $this->user,
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->guard->setCredential($credential);

    expect($this->guard)
        ->user()->toBe($this->user);
});

it('queries the /userinfo endpoint', function (): void {
    $credential = CredentialEntity::create(
        user: $this->user,
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->guard->setCredential($credential, Guard::SOURCE_TOKEN);

    expect($this->guard)
        ->user()->toBe($this->user);

    $identifier = 'updated|' . uniqid();

    $response = (new MockResponseFactory)->createResponse();

    $this->guard
        ->sdk()
        ->configuration()
        ->getHttpClient()
        ->addResponseWildcard($response->withBody(
            (new MockStreamFactory)->createStream(
                json_encode(
                    value: [
                        'sub' => $identifier,
                        'name' => 'John Doe',
                        'email' => '...',
                    ],
                    flags: JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
                )
            )
        )
    );

    $userAttributes = $this->guard->getRefreshedUser()->getAttributes();

    expect($userAttributes)
        ->toBeArray()
        ->toMatchArray([
            'sub' => $identifier,
        ]);
});

test('hasPermission(*) returns true for wildcard', function (): void {
    $credential = CredentialEntity::create(
        user: $this->user,
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->guard->setCredential($credential, Guard::SOURCE_TOKEN);

    expect($this->guard->hasPermission('*'))
        ->toBeTrue();
});

test('hasPermission() returns true for matches', function (): void {
    $credential = CredentialEntity::create(
        user: $this->user,
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenDecoded: [
            'permissions' => [
                'read:posts',
                'read:messages',
                'read:users',
            ],
        ],
        accessTokenExpiration: time() + 3600
    );

    $this->guard->setCredential($credential, Guard::SOURCE_TOKEN);

    expect($this->guard->hasPermission('read:messages'))
        ->toBeTrue();

    expect($this->guard->hasPermission('write:posts'))
        ->toBeFalse();
});

test('hasPermission() returns false when there are no permissions', function (): void {
    $credential = CredentialEntity::create(
        user: $this->user,
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenDecoded: [
            'permissions' => [],
        ],
        accessTokenExpiration: time() + 3600
    );

    $this->guard->setCredential($credential, Guard::SOURCE_TOKEN);

    expect($this->guard->hasPermission('read:messages'))
        ->toBeFalse();
});

test('management() returns a Management API class', function (): void {
    $credential = CredentialEntity::create(
        user: $this->user,
        idToken: mockIdToken(algorithm: Token::ALGO_HS256),
        accessToken: mockAccessToken(algorithm: Token::ALGO_HS256),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenDecoded: [
            'permissions' => [],
        ],
        accessTokenExpiration: time() + 3600
    );

    $this->guard->setCredential($credential, Guard::SOURCE_TOKEN);

    expect($this->guard->management())
        ->toBeInstanceOf(ManagementInterface::class);
});

test('sdk() uses the guard name to optionally merge configuration data', function (): void {
    config([
        'auth0.guards.default.domain' => 'https://default-domain.com',
        'auth0.guards.web.strategy' => 'none',
        'auth0.guards.web.domain' => 'https://legacy-domain.com',
    ]);

    expect($this->guard->sdk()->configuration()->getDomain())
        ->toBe('legacy-domain.com');
});

test('sdk() configuration v1 is supported', function (): void {
    config(['auth0' => [
        'strategy' => 'none',
        'domain' => 'https://v1-domain.com',
    ]]);

    expect($this->guard->sdk()->configuration()->getDomain())
        ->toBe('v1-domain.com');
});

test('sdk() configuration v1 defaults to an empty array', function (): void {
    config(['auth0' => 123]);
    $this->guard->sdk()->configuration()->getDomain();
})->throws(ConfigurationException::class);
