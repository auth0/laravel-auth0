<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Entities\Credential;
use Auth0\Laravel\Model\Stateful\User;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\getJson;

uses()->group('auth', 'auth.guard', 'auth.guard.stateful');

beforeEach(function (): void {
    $this->laravel = app('auth0');
    $this->guard = $guard = auth('testGuard');
    $this->sdk = $this->laravel->getSdk();
    $this->config = $this->sdk->configuration();
    $this->session = $this->config->getSessionStorage();
    $this->transient = $this->config->getTransientStorage();
    $this->user = new User(['sub' => uniqid('auth0|')]);

    $this->secret = uniqid();

    $this->config->setDomain('my-domain.auth0.com');
    $this->config->setClientId('my_client_id');
    $this->config->setClientSecret($this->secret);
    $this->config->setCookieSecret('my_cookie_secret');
    $this->config->setStrategy(SdkConfiguration::STRATEGY_REGULAR);

    $this->session->set('user', ['sub' => 'hello|world']);
    $this->session->set('idToken', uniqid());
    $this->session->set('accessToken', uniqid());
    $this->session->set('accessTokenScope', [uniqid()]);
    $this->session->set('accessTokenExpiration', time() + 60);

    $this->route = '/' . uniqid();

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

    $http->queueResponse(
        $responseFactory::create(
            body: $streamFactory->createStream(
                json_encode([
                    'access_token' => uniqid(),
                    'expires_in' => 60,
                    'scope' => 'openid profile',
                    'token_type' => 'Bearer',
                ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
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
