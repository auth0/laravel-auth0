<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Entities\Credential;
use Auth0\Laravel\Model\Stateless\User;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Auth0\SDK\Token;
use Auth0\SDK\Token\Generator;
use PsrMock\Psr18\Client as MockHttpClient;
use PsrMock\Psr17\RequestFactory as MockRequestFactory;
use PsrMock\Psr17\ResponseFactory as MockResponseFactory;
use PsrMock\Psr17\StreamFactory as MockStreamFactory;

use function Pest\Laravel\getJson;

uses()->group('auth', 'auth.guard', 'auth.guard.session');

beforeEach(function (): void {
    $this->secret = uniqid();

    config([
        'auth0.strategy' => SdkConfiguration::STRATEGY_API,
        'auth0.domain' => uniqid() . '.auth0.com',
        'auth0.clientId' => uniqid(),
        'auth0.audience' => ['https://example.com/health-api'],
        'auth0.clientSecret' => $this->secret,
        'auth0.tokenAlgorithm' => Token::ALGO_HS256,
        'auth0.routes.home' => '/' . uniqid(),
    ]);

    $this->laravel = app('auth0');
    $this->guard = $guard = auth('tokenGuard');
    $this->sdk = $this->laravel->getSdk();
    $this->config = $this->sdk->configuration();

    $this->identifier = 'auth0|' . uniqid();

    $this->token = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => 'https://' . config('auth0.domain') . '/',
        "sub" => $this->identifier,
        "aud" => [
            config('auth0.audience')[0],
            "https://my-domain.auth0.com/userinfo"
        ],
        "azp" => config('auth0.clientId'),
        "exp" => time() + 60,
        "iat" => time(),
        "scope" => "openid profile read:patients read:admin"
    ]);
    $this->bearerToken = ['Authorization' => 'Bearer ' . $this->token->toString()];

    $this->route = '/' . uniqid();
    $this->route2 = '/' . uniqid();
    $guard = $this->guard;

    Route::get($this->route, function () use ($guard) {
        $credential = $guard->find(Guard::SOURCE_TOKEN);

        if (null !== $credential) {
            $guard->login($credential, Guard::SOURCE_TOKEN);

            return response()->json(['status' => 'OK', 'user' => $guard->user(Guard::SOURCE_TOKEN)->getAuthIdentifier()]);
        }

        abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
    });

    Route::get($this->route2, function () use ($guard) {
        return response()->json(['user' => $guard->user(Guard::SOURCE_TOKEN)?->getAuthIdentifier()]);
    });
});

it('assigns a user with login() from a good token', function (): void {
    expect($this->guard)
        ->user()->toBeNull();

    getJson($this->route, $this->bearerToken)
        ->assertStatus(Response::HTTP_OK);

    expect($this->guard)
        ->user()->not()->toBeNull();
});

it('assigns a user with user() from a good token', function (): void {
    expect($this->guard)
        ->user()->toBeNull();

    getJson($this->route2, $this->bearerToken)
        ->assertStatus(Response::HTTP_OK);

    expect($this->guard)
        ->user()->not()->toBeNull();
});

// it('does not assign a user from a empty token', function (): void {
//     getJson($this->route, ['Authorization' => 'Bearer '])
//         ->assertStatus(Response::HTTP_UNAUTHORIZED);

//     expect($this->guard)
//         ->user()->toBeNull();
// });

// it('does not get a user from a bad token', function (): void {
//     $this->config->setAudience(['BAD_AUDIENCE']);

//     expect($this->guard)
//         ->user()->toBeNull();

//     getJson($this->route, $this->bearerToken)
//         ->assertStatus(Response::HTTP_UNAUTHORIZED);

//     expect($this->guard)
//         ->user()->toBeNull();
// });

it('does not query the /userinfo endpoint for refreshUser() without a bearer token', function (): void {
    expect($this->guard)
        ->user()->toBeNull();

    $this->guard->setCredential(new Credential(
        user: new User(['sub' => $this->identifier]),
    ));

    expect($this->guard)
        ->user()->not()->toBeNull();

    $client = new MockHttpClient(requestLimit: 0);

    $this->config->setHttpClient($client);

    $this->guard->refreshUser();

    expect($this->guard)
        ->user()->not()->toBeNull();
});

it('aborts querying the /userinfo endpoint for refreshUser() when a bad response is received', function (): void {
    expect($this->guard)
        ->user()->toBeNull();

    getJson($this->route2, $this->bearerToken)
        ->assertStatus(Response::HTTP_OK);

    expect($this->guard)
        ->user()->getAuthIdentifier()->toBe($this->identifier);

    $requestFactory = new MockRequestFactory;
    $responseFactory = new MockResponseFactory;
    $streamFactory = new MockStreamFactory;

    $response = $responseFactory->createResponse(200);
    $response->getBody()->write(json_encode(
        true,
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
        ->sub->toBe($this->identifier);
});

it('queries the /userinfo endpoint for refreshUser()', function (): void {
    expect($this->guard)
        ->user()->toBeNull();

    getJson($this->route2, $this->bearerToken)
        ->assertStatus(Response::HTTP_OK);

    expect($this->guard)
        ->user()->getAuthIdentifier()->toBe($this->identifier);

    $requestFactory = new MockRequestFactory;
    $responseFactory = new MockResponseFactory;
    $streamFactory = new MockStreamFactory;

    $response = $responseFactory->createResponse(200);
    $response->getBody()->write(json_encode(
        [
            'sub' => $this->identifier,
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
            'sub' => $this->identifier,
            'name' => 'John Doe',
            'email' => '...',
        ]);
});
