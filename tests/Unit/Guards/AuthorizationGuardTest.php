<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Entities\CredentialEntity;
use Auth0\Laravel\Users\StatelessUser;
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
        'auth0.default.strategy' => SdkConfiguration::STRATEGY_API,
        'auth0.default.domain' => uniqid() . '.auth0.com',
        'auth0.default.clientId' => uniqid(),
        'auth0.default.audience' => ['https://example.com/health-api'],
        'auth0.default.clientSecret' => $this->secret,
        'auth0.default.tokenAlgorithm' => Token::ALGO_HS256,
    ]);

    $this->laravel = app('auth0');
    $this->guard = $guard = auth('tokenGuard');
    $this->sdk = $this->laravel->getSdk();
    $this->config = $this->sdk->configuration();

    $this->identifier = 'auth0|' . uniqid();

    $this->token = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => 'https://' . config('auth0.default.domain') . '/',
        "sub" => $this->identifier,
        "aud" => [
            config('auth0.default.audience')[0],
            "https://my-domain.auth0.com/userinfo"
        ],
        "azp" => config('auth0.default.clientId'),
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

    $this->guard->setCredential(new CredentialEntity(
        user: new StatelessUser(['sub' => $this->identifier]),
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

    $response = (new MockResponseFactory)->createResponse();

    $this->guard
        ->sdk()
        ->configuration()
        ->getHttpClient()
        ->addResponseWildcard($response->withBody(
            (new MockStreamFactory)->createStream(
                json_encode(
                    value: true,
                    flags: JSON_PRETTY_PRINT
                )
            )
        )
    );

    $this->guard->refreshUser();

    $userAttributes = $this->guard->user()->getAttributes();

    expect($userAttributes)
        ->toBeArray()
        ->toHaveKey('sub', $this->identifier);
});

it('queries the /userinfo endpoint for refreshUser()', function (): void {
    expect($this->guard)
        ->user()->toBeNull();

    getJson($this->route2, $this->bearerToken)
        ->assertStatus(Response::HTTP_OK);

    expect($this->guard)
        ->user()->getAuthIdentifier()->toBe($this->identifier);

    $response = (new MockResponseFactory)->createResponse();

    $this->guard
        ->sdk()
        ->configuration()
        ->getHttpClient()
        ->addResponseWildcard($response->withBody(
            (new MockStreamFactory)->createStream(
                json_encode(
                    value: [
                        'sub' => $this->identifier,
                        'name' => 'John Doe',
                        'email' => '...',
                    ],
                    flags: JSON_PRETTY_PRINT
                )
            )
        )
    );

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
