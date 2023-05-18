<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Entities\CredentialEntity;
use Auth0\Laravel\Users\StatefulUser;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token\Generator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use PsrMock\Psr18\Client as MockHttpClient;
use PsrMock\Psr17\RequestFactory as MockRequestFactory;
use PsrMock\Psr17\ResponseFactory as MockResponseFactory;
use PsrMock\Psr17\StreamFactory as MockStreamFactory;

use function Pest\Laravel\getJson;

uses()->group('auth', 'auth.guard', 'auth.guard.stateful');

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
    $this->guard = $guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();
    $this->config = $this->sdk->configuration();
    $this->session = $this->config->getSessionStorage();

    $this->user = new StatefulUser(['sub' => uniqid('auth0|')]);

    $this->session->set('user', ['sub' => 'hello|world']);
    $this->session->set('idToken', (string) Generator::create((createRsaKeys())->private));
    $this->session->set('accessToken', (string) Generator::create((createRsaKeys())->private));
    $this->session->set('accessTokenScope', [uniqid()]);
    $this->session->set('accessTokenExpiration', time() + 60);

    $this->route = '/' . uniqid();
    $guard = $this->guard;

    Route::get($this->route, function () use ($guard) {
        $credential = $guard->find(Guard::SOURCE_SESSION);

        if (null !== $credential) {
            $guard->login($credential, Guard::SOURCE_SESSION);
            return response()->json(['status' => 'OK']);
        }

        abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
    });
});

it('gets a user from a valid session', function (): void {
    getJson($this->route)
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
    $user = new StatefulUser(['sub' => $identifier]);

    // Overwrite state using the Guard's login()
    $this->guard->login(CredentialEntity::create(
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
    // $idToken = uniqid('id-token-');
    // $accessToken = uniqid('access-token-');
    $accessTokenScope = [uniqid('access-token-scope-')];
    $accessTokenExpiration = time() + 60;

    $this->session->set('user', ['sub' => $identifier]);
    // $this->session->set('idToken', $idToken);
    // $this->session->set('accessToken', $accessToken);
    $this->session->set('accessTokenScope', $accessTokenScope);
    $this->session->set('accessTokenExpiration', $accessTokenExpiration);

    $found = $this->guard->find(Guard::SOURCE_SESSION);

    expect($found)
        ->toBeInstanceOf(CredentialEntity::class);

    $this->guard->login($found, Guard::SOURCE_SESSION);

    expect($this->session)
        ->get('user')->toBe(['sub' => $identifier])
        // ->get('idToken')->toBe($idToken)
        // ->get('accessToken')->toBe($accessToken)
        ->get('accessTokenScope')->toBe($accessTokenScope)
        ->get('accessTokenExpiration')->toBe($accessTokenExpiration)
        ->get('refreshToken')->toBeNull();

    $user = new StatefulUser(['sub' => $identifier]);

    $changedIdToken = uniqid('CHANGED-id-token-');
    $changedRefreshToken = uniqid('CHANGED-refresh-token-');

    // Overwrite state using the Guard's login()
    $this->guard->login(CredentialEntity::create(
        user: $user,
        idToken: $changedIdToken,
        refreshToken: $changedRefreshToken
    ), Guard::SOURCE_SESSION);

    expect($this->guard)
        ->user()->getAuthIdentifier()->toBe($identifier);

    expect($this->session)
        ->get('user')->toBe(['sub' => $identifier])
        ->get('idToken')->toBe($changedIdToken)
        // ->get('accessToken')->toBe($accessToken)
        ->get('accessTokenScope')->toBe($accessTokenScope)
        ->get('accessTokenExpiration')->toBe($accessTokenExpiration)
        ->get('refreshToken')->toBe($changedRefreshToken);
});

it('queries the /userinfo endpoint for refreshUser()', function (): void {
    $identifier = uniqid('auth0|');
    // $idToken = uniqid('id-token-');
    // $accessToken = uniqid('access-token-');
    $accessTokenScope = [uniqid('access-token-scope-')];
    $accessTokenExpiration = time() + 60;

    $this->session->set('user', ['sub' => $identifier]);
    // $this->session->set('idToken', $idToken);
    // $this->session->set('accessToken', $accessToken);
    $this->session->set('accessTokenScope', $accessTokenScope);
    $this->session->set('accessTokenExpiration', $accessTokenExpiration);

    $found = $this->guard->find(Guard::SOURCE_SESSION);
    $this->guard->login($found, Guard::SOURCE_SESSION);

    expect($this->session)
        ->get('user')->toBe(['sub' => $identifier]);

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
    // $idToken = uniqid('id-token-');
    $accessTokenScope = [uniqid('access-token-scope-')];
    $accessTokenExpiration = time() + 60;

    $this->session->set('user', ['sub' => $identifier]);
    // $this->session->set('idToken', $idToken);
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

    $http = new MockHttpClient(fallbackResponse: $response, requestLimit: 0);
    $http->addResponseWildcard($response);

    $this->config->setHttpRequestFactory($requestFactory);
    $this->config->setHttpResponseFactory($responseFactory);
    $this->config->setHttpStreamFactory($streamFactory);
    $this->config->setHttpClient($http);

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
    // $idToken = uniqid('id-token-');
    // $accessToken = uniqid('access-token-');
    $accessTokenScope = [uniqid('access-token-scope-')];
    $accessTokenExpiration = time() + 60;

    $this->session->set('user', ['sub' => $identifier]);
    // $this->session->set('idToken', $idToken);
    // $this->session->set('accessToken', $accessToken);
    $this->session->set('accessTokenScope', $accessTokenScope);
    $this->session->set('accessTokenExpiration', $accessTokenExpiration);

    $found = $this->guard->find(Guard::SOURCE_SESSION);
    $this->guard->login($found, Guard::SOURCE_SESSION);

    expect($this->session)
        ->get('user')->toBe(['sub' => $identifier]);

    $response = (new MockResponseFactory)->createResponse();

    $this->guard
        ->sdk()
        ->configuration()
        ->getHttpClient()
        ->addResponseWildcard($response->withBody(
            (new MockStreamFactory)->createStream(
                json_encode(
                    value: 'bad response',
                    flags: JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
                )
            )
        )
    );

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

it('successfully continues a session when an access token is successfully refreshed', function (): void {
    $this->session->set('accessTokenExpiration', time() - 1000);
    $this->session->set('refreshToken', (string) Generator::create((createRsaKeys())->private));

    $response = (new MockResponseFactory)->createResponse();

    $this->guard
        ->sdk()
        ->configuration()
        ->getHttpClient()
        ->addResponseWildcard($response->withBody(
            (new MockStreamFactory)->createStream(
                json_encode(
                    value: [
                        'access_token' => (string) Generator::create((createRsaKeys())->private),
                        'expires_in' => 60,
                        'scope' => 'openid profile',
                        'token_type' => 'Bearer',
                    ],
                    flags: JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR
                )
            )
        )
    );

    $found = $this->guard->find(Guard::SOURCE_SESSION);
    $this->guard->login($found, Guard::SOURCE_SESSION);

    expect($this->guard)
        ->user()->not()->toBeNull();

    expect($this->session)
        ->get('user')->not()->toBeNull();
});
