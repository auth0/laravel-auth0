<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Entities\Credential;
use Auth0\Laravel\Model\Stateful\User;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use PsrMock\Psr18\Client as MockHttpClient;
use PsrMock\Psr17\RequestFactory as MockRequestFactory;
use PsrMock\Psr17\ResponseFactory as MockResponseFactory;
use PsrMock\Psr17\StreamFactory as MockStreamFactory;

use function Pest\Laravel\getJson;

uses()->group('auth', 'auth.guard', 'auth.guard.session');

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
    $this->guard = $guard = auth('sessionGuard');
    $this->sdk = $this->laravel->getSdk();
    $this->config = $this->sdk->configuration();
    $this->session = $this->config->getSessionStorage();

    $this->user = new User(['sub' => uniqid('auth0|')]);

    $this->session->set('user', ['sub' => 'hello|world']);
    $this->session->set('idToken', uniqid());
    $this->session->set('accessToken', uniqid());
    $this->session->set('accessTokenScope', [uniqid()]);
    $this->session->set('accessTokenExpiration', time() + 60);

    $this->route = '/' . uniqid();
    $this->route2 = '/' . uniqid();
    $guard = $this->guard;

    Route::get($this->route, function () use ($guard) {
        $credential = $guard->find(Guard::SOURCE_SESSION);

        if (null !== $credential) {
            $guard->login($credential, Guard::SOURCE_SESSION);
            return response()->json(['status' => 'OK']);
        }

        abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
    });

    Route::get($this->route2, function () use ($guard) {
        return response()->json(['user' => $guard->user(Guard::SOURCE_SESSION)->getAuthIdentifier()]);
    });
});

it('gets a user from a valid session using find()', function (): void {
    getJson($this->route)
        ->assertStatus(Response::HTTP_OK);

    expect($this->guard)
        ->user()->getAuthIdentifier()->toBe('hello|world');
});

it('gets a user from a valid session using user()', function (): void {
    getJson($this->route2)
        ->assertStatus(Response::HTTP_OK);

    expect($this->guard)
        ->user()->getAuthIdentifier()->toBe('hello|world');
});

it('updates internal and session states as appropriate', function (): void {
    // Session should be available and populated
    expect($this->session)
        ->getAll()->not()->toBe([]);

    // Guard should pick up on the session during the HTTP request
    getJson($this->route)
        ->assertStatus(Response::HTTP_OK);

    // Guard should have it's state populated
    expect($this->guard)
        ->user()->getAuthIdentifier()->toBe('hello|world');

    // Empty guard state
    $this->guard->logout();

    // Guard should have had it's state emptied
    expect($this->guard)
        ->user()->toBeNull();

    // Session should have been emptied
    expect($this->session)
        ->getAll()->toBe([]);

    // HTTP request should fail without a session.
    getJson($this->route)
        ->assertStatus(Response::HTTP_UNAUTHORIZED);

    // Inject a new session into the store
    $this->session->set('user', ['sub' => 'hello|world|two']);

    // Session should be available and populated again
    expect($this->session)
        ->getAll()->not()->toBe([]);

    getJson($this->route)
        ->assertStatus(Response::HTTP_OK);

    // Guard should pick up on the session
    expect($this->guard)
        ->user()->getAuthIdentifier()->toBe('hello|world|two');

    // Directly wipe the Laravel session, circumventing the Guard APIs
    $this->session->purge();

    // Session should be empty
    expect($this->session)
        ->getAll()->toBe([]);

    getJson($this->route)
        ->assertStatus(Response::HTTP_UNAUTHORIZED);

    // Guard should have it's state emptied
    expect($this->guard)
        ->user()->toBeNull();

    $this->session->set('user', ['sub' => 'hello|world|4']);

    // Session should be available
    expect($this->session)
        ->getAll()->not()->toBe([]);

    getJson($this->route)
        ->assertStatus(Response::HTTP_OK);

    // Guard should pick up on the session
    expect($this->guard)
        ->user()->getAuthIdentifier()->toBe('hello|world|4');

    $identifier = uniqid('auth0|');
    $user = new User(['sub' => $identifier]);

    // Overwrite state using the Guard's login()
    $this->guard->login(Credential::create(
        user: $user
    ), Guard::SOURCE_SESSION);

    getJson($this->route)
        ->assertStatus(Response::HTTP_OK);

    // Guard should have it's state updated
    expect($this->guard)
        ->user()->getAuthIdentifier()->toBe($identifier);

    // Session should be updated
    expect($this->session)
        ->get('user')->toBe(['sub' => $identifier]);
});

it('creates a session from login()', function (): void {
    $identifier = uniqid('auth0|');
    $idToken = uniqid('id-token-');
    $accessToken = uniqid('access-token-');
    $accessTokenScope = [uniqid('access-token-scope-')];
    $accessTokenExpiration = time() + 60;

    $this->session->set('user', ['sub' => $identifier]);
    $this->session->set('idToken', $idToken);
    $this->session->set('accessToken', $accessToken);
    $this->session->set('accessTokenScope', $accessTokenScope);
    $this->session->set('accessTokenExpiration', $accessTokenExpiration);

    $found = $this->guard->find(Guard::SOURCE_SESSION);

    expect($found)
        ->toBeInstanceOf(Credential::class);

    $this->guard->login($found, Guard::SOURCE_SESSION);

    expect($this->session)
        ->get('user')->toBe(['sub' => $identifier])
        ->get('idToken')->toBe($idToken)
        ->get('accessToken')->toBe($accessToken)
        ->get('accessTokenScope')->toBe($accessTokenScope)
        ->get('accessTokenExpiration')->toBe($accessTokenExpiration)
        ->get('refreshToken')->toBeNull();

    $user = new User(['sub' => $identifier]);

    $changedIdToken = uniqid('CHANGED-id-token-');
    $changedRefreshToken = uniqid('CHANGED-refresh-token-');

    // Overwrite state using the Guard's login()
    $this->guard->login(Credential::create(
        user: $user,
        idToken: $changedIdToken,
        refreshToken: $changedRefreshToken
    ), Guard::SOURCE_SESSION);

    expect($this->guard)
        ->user()->getAuthIdentifier()->toBe($identifier);

    expect($this->session)
        ->get('user')->toBe(['sub' => $identifier])
        ->get('idToken')->toBe($changedIdToken)
        ->get('accessToken')->toBe($accessToken)
        ->get('accessTokenScope')->toBe($accessTokenScope)
        ->get('accessTokenExpiration')->toBe($accessTokenExpiration)
        ->get('refreshToken')->toBe($changedRefreshToken);
});

it('queries the /userinfo endpoint for refreshUser()', function (): void {
    $identifier = uniqid('auth0|');
    $idToken = uniqid('id-token-');
    $accessToken = uniqid('access-token-');
    $accessTokenScope = [uniqid('access-token-scope-')];
    $accessTokenExpiration = time() + 60;

    $this->session->set('user', ['sub' => $identifier]);
    $this->session->set('idToken', $idToken);
    $this->session->set('accessToken', $accessToken);
    $this->session->set('accessTokenScope', $accessTokenScope);
    $this->session->set('accessTokenExpiration', $accessTokenExpiration);

    $found = $this->guard->find(Guard::SOURCE_SESSION);
    $this->guard->login($found, Guard::SOURCE_SESSION);

    expect($this->session)
        ->get('user')->toBe(['sub' => $identifier]);

    $requestFactory = new MockRequestFactory;
    $responseFactory = new MockResponseFactory;
    $streamFactory = new MockStreamFactory;

    $response = $responseFactory->createResponse(200);
    $response->getBody()->write(json_encode(
        [
            'sub' => $identifier,
            'name' => 'John Doe',
            'email' => '...',
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
            'name' => 'John Doe',
            'email' => '...',
        ]);
});

it('does not query the /userinfo endpoint for refreshUser() if an access token is not available', function (): void {
    $identifier = uniqid('auth0|');
    $idToken = uniqid('id-token-');
    $accessTokenScope = [uniqid('access-token-scope-')];
    $accessTokenExpiration = time() + 60;

    $this->session->set('user', ['sub' => $identifier]);
    $this->session->set('idToken', $idToken);
    $this->session->set('accessToken', null);
    $this->session->set('accessTokenScope', $accessTokenScope);
    $this->session->set('accessTokenExpiration', $accessTokenExpiration);

    $found = $this->guard->find(Guard::SOURCE_SESSION);
    $this->guard->login($found, Guard::SOURCE_SESSION);

    expect($this->session)
        ->get('user')->toBe(['sub' => $identifier]);

    $requestFactory = new MockRequestFactory;
    $responseFactory = new MockResponseFactory;
    $streamFactory = new MockStreamFactory;

    $response = $responseFactory->createResponse(200);
    $response->getBody()->write(json_encode(
        [
            'sub' => $identifier,
            'name' => 'John Doe',
            'email' => '...',
        ],
        JSON_PRETTY_PRINT
    ));

    $client = new MockHttpClient(fallbackResponse: $response, requestLimit: 0);

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

it('rejects bad responses from the /userinfo endpoint for refreshUser()', function (): void {
    $identifier = uniqid('auth0|');
    $idToken = uniqid('id-token-');
    $accessToken = uniqid('access-token-');
    $accessTokenScope = [uniqid('access-token-scope-')];
    $accessTokenExpiration = time() + 60;

    $this->session->set('user', ['sub' => $identifier]);
    $this->session->set('idToken', $idToken);
    $this->session->set('accessToken', $accessToken);
    $this->session->set('accessTokenScope', $accessTokenScope);
    $this->session->set('accessTokenExpiration', $accessTokenExpiration);

    $found = $this->guard->find(Guard::SOURCE_SESSION);
    $this->guard->login($found, Guard::SOURCE_SESSION);

    expect($this->session)
        ->get('user')->toBe(['sub' => $identifier]);

    $requestFactory = new MockRequestFactory;
    $responseFactory = new MockResponseFactory;
    $streamFactory = new MockStreamFactory;

    $response = $responseFactory->createResponse(200);
    $response->getBody()->write(json_encode('bad response', JSON_PRETTY_PRINT));

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

it('immediately invalidates an expired session when a refresh token is not available', function (): void {
    $this->session->set('accessTokenExpiration', time() - 1000);

    $found = $this->guard->find(Guard::SOURCE_SESSION);
    $this->guard->login($found, Guard::SOURCE_SESSION);

    expect($this->guard)
        ->user()->toBeNull();

    expect($this->session)
        ->get('user')->toBeNull();
});

it('invalidates an expired session when an access token fails to refresh', function (): void {
    $this->session->set('accessTokenExpiration', time() - 1000);
    $this->session->set('refreshToken', uniqid());

    $found = $this->guard->find(Guard::SOURCE_SESSION);
    $this->guard->login($found, Guard::SOURCE_SESSION);

    expect($this->guard)
        ->user()->toBeNull();

    expect($this->session)
        ->get('user')->toBeNull();
});

it('successfully continues a session when an access token succeeds is renewed', function (): void {
    $this->session->set('accessTokenExpiration', time() - 1000);
    $this->session->set('refreshToken', uniqid());

    $http = $this->config->getHttpClient();
    $streamFactory = $this->config->getHttpStreamFactory();
    $responseFactory = $this->config->getHttpResponseFactory();

    $response = $responseFactory->createResponse();

    $response = $response->withBody(
        $streamFactory->createStream(
            json_encode(
                value: [
                    'access_token' => uniqid(),
                    'expires_in' => 60,
                    'scope' => 'openid profile',
                    'token_type' => 'Bearer',
                ],
                flags: JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
            )
        )
    );

    $http->addResponseWildcard($response);

    $found = $this->guard->find(Guard::SOURCE_SESSION);
    $this->guard->login($found, Guard::SOURCE_SESSION);

    expect($this->guard)
        ->user()->not()->toBeNull();

    expect($this->session)
        ->get('user')->not()->toBeNull();
});
