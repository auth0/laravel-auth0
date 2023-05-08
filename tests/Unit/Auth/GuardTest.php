<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Contract\Exception\AuthenticationException as AuthenticationExceptionContract;
use Auth0\Laravel\Exception\AuthenticationException;
use Auth0\Laravel\Entities\Credential;
use Auth0\Laravel\Model\Stateful\User;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Support\Facades\Route;
use PsrMock\Psr18\Client as MockHttpClient;
use PsrMock\Psr17\RequestFactory as MockRequestFactory;
use PsrMock\Psr17\ResponseFactory as MockResponseFactory;
use PsrMock\Psr17\StreamFactory as MockStreamFactory;

uses()->group('auth', 'auth.guard', 'auth.guard.shared');

beforeEach(function (): void {
    $this->secret = uniqid();

    config([
        'auth0.strategy' => SdkConfiguration::STRATEGY_REGULAR,
        'auth0.domain' => uniqid() . '.auth0.com',
        'auth0.clientId' => uniqid(),
        'auth0.clientSecret' => $this->secret,
        'auth0.cookieSecret' => uniqid(),
        'auth0.routes.home' => '/' . uniqid(),
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();
    $this->config = $this->sdk->configuration();
    $this->session = $this->config->getSessionStorage();

    $this->user = new User(['sub' => uniqid('auth0|')]);

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

    $this->guard->login(Credential::create(
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

    $this->guard->login(Credential::create(
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

it('checks if a user is logged in', function (): void {
    expect($this->guard)
        ->check()->toBeFalse();

    $this->guard->login(Credential::create(
        user: $this->user
    ));

    expect($this->guard)
        ->check()->toBeTrue();
});

it('checks if a user is a guest', function (): void {
    expect($this->guard)
        ->guest()->toBeTrue();

    $this->guard->login(Credential::create(
        user: $this->user
    ));

    expect($this->guard)
        ->guest()->toBeFalse();
});

it('gets the user identifier', function (): void {
    $this->guard->login(Credential::create(
        user: $this->user
    ));

    expect($this->guard)
        ->id()->toBe($this->user->getAuthIdentifier());
});

it('validates a user', function (): void {
    $this->guard->login(Credential::create(
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

it('has a scope', function (): void {
    $this->user = new User(['sub' => uniqid('auth0|'), 'scope' => 'read:users 456']);

    $credential = Credential::create(
        user: $this->user,
        accessTokenScope: ['read:users', '456']
    );

    expect($this->guard)
        ->hasScope('read:users', $credential)->toBeTrue()
        ->hasScope('123', $credential)->toBeFalse()
        ->hasScope('456', $credential)->toBeTrue()
        ->hasScope('789', $credential)->toBeFalse()
        ->hasScope('*', $credential)->toBeTrue();

    $credential = Credential::create(
        user: $this->user
    );

    expect($this->guard)
        ->hasScope('read:users', $credential)->toBeFalse()
        ->hasScope('*', $credential)->toBeTrue();
});

it('checks if a user was authenticated via remember', function (): void {
    $this->guard->login(Credential::create(
        user: $this->user
    ));

    expect($this->guard)
        ->viaRemember()->toBeFalse();
});

it('returns null if authenticate() is called without being authenticated', function (): void {
    $response = $this->guard->authenticate();
    expect($response)->toBeNull();
})->throws(AuthenticationException::class, AuthenticationExceptionContract::UNAUTHENTICATED);

it('returns a user from authenticate() if called while authenticated', function (): void {
    $this->guard->login(Credential::create(
        user: $this->user
    ));

    $response = $this->guard->authenticate();

    expect($response)
        ->toBe($this->user);
});

it('gets/sets a credentials', function (): void {
    $credential = Credential::create(
        user: $this->user,
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->guard->setCredential($credential);

    expect($this->guard)
        ->user()->toBe($this->user);
});

it('queries the /userinfo endpoint', function (): void {
    $credential = Credential::create(
        user: $this->user,
        idToken: uniqid(),
        accessToken: uniqid(),
        accessTokenScope: ['openid', 'profile', 'email', 'read:messages'],
        accessTokenExpiration: time() + 3600
    );

    $this->guard->setCredential($credential, Guard::SOURCE_TOKEN);

    expect($this->guard)
        ->user()->toBe($this->user);

    $requestFactory = new MockRequestFactory;
    $responseFactory = new MockResponseFactory;
    $streamFactory = new MockStreamFactory;

    $identifier = 'updated|' . uniqid();

    $response = $responseFactory->createResponse(200);
    $response->getBody()->write(json_encode(
        [
            'sub' => $identifier,
        ],
        JSON_PRETTY_PRINT
    ));

    $client = new MockHttpClient(fallbackResponse: $response);

    $this->config->setHttpRequestFactory($requestFactory);
    $this->config->setHttpResponseFactory($responseFactory);
    $this->config->setHttpStreamFactory($streamFactory);
    $this->config->setHttpClient($client);

    $this->guard->refreshUser();

    $userAttributes = $this->guard->user()->getAttributes();

    expect($userAttributes)
        ->toBeArray()
        ->toMatchArray([
            'sub' => $identifier,
        ]);
});
