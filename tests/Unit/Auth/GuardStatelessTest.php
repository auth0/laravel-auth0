<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\Guard;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;
use Auth0\SDK\Token\Generator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\getJson;

uses()->group('auth', 'auth.guard', 'auth.guard.stateless');

beforeEach(function (): void {
    $this->secret = uniqid();

    config([
        'auth0.AUTH0_CONFIG_VERSION' => 2,
        'auth0.guards.default.strategy' => SdkConfiguration::STRATEGY_API,
        'auth0.guards.default.domain' => uniqid() . '.auth0.com',
        'auth0.guards.default.clientId' => uniqid(),
        'auth0.guards.default.audience' => ['https://example.com/health-api'],
        'auth0.guards.default.clientSecret' => $this->secret,
        'auth0.guards.default.tokenAlgorithm' => Token::ALGO_HS256,
    ]);

    $this->laravel = app('auth0');
    $this->guard = auth('legacyGuard');
    $this->sdk = $this->laravel->getSdk();
    $this->config = $this->sdk->configuration();

    $this->token = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => 'https://' . config('auth0.guards.default.domain') . '/',
        "sub" => "auth0|123456",
        "aud" => [
            config('auth0.guards.default.audience')[0],
            "https://my-domain.auth0.com/userinfo"
        ],
        "azp" => config('auth0.guards.default.clientId'),
        "exp" => time() + 60,
        "iat" => time(),
        "scope" => "openid profile read:patients read:admin"
    ]);
    $this->bearerToken = ['Authorization' => 'Bearer ' . $this->token->toString()];

    $this->route = '/' . uniqid();
    $guard = $this->guard;

    Route::get($this->route, function () use ($guard) {
        $credential = $guard->find(Guard::SOURCE_TOKEN);

        if (null !== $credential) {
            $guard->login($credential, Guard::SOURCE_TOKEN);
            return response()->json(['status' => 'OK']);
        }

        abort(Response::HTTP_UNAUTHORIZED, 'Unauthorized');
    });
});

it('assigns a user from a good token', function (): void {
    expect($this->guard)
        ->user()->toBeNull();

    getJson($this->route, $this->bearerToken)
        ->assertStatus(Response::HTTP_OK);

    expect($this->guard)
        ->user()->not()->toBeNull();
});

it('does not assign a user from a empty token', function (): void {
    getJson($this->route, ['Authorization' => 'Bearer '])
        ->assertStatus(Response::HTTP_UNAUTHORIZED);

    expect($this->guard)
        ->user()->toBeNull();
});

it('does not get a user from a bad token', function (): void {
    $this->guard
        ->sdk()
        ->configuration()
        ->setAudience(['BAD_AUDIENCE']);

    expect($this->guard)
        ->user()->toBeNull();

    getJson($this->route, $this->bearerToken)
        ->assertStatus(Response::HTTP_UNAUTHORIZED);

    expect($this->guard)
        ->user()->toBeNull();
});
