<?php

declare(strict_types=1);

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Contract\Auth\User\Repository;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Token;
use Auth0\SDK\Token\Generator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\getJson;

uses()->group('auth', 'auth.guard', 'auth.guard.stateless');

beforeEach(function (): void {
    $this->laravel = app('auth0');
    $this->guard = $guard = auth('testGuard');
    $this->sdk = $this->laravel->getSdk();
    $this->config = $this->sdk->configuration();

    $this->secret = uniqid();

    $this->config->setDomain('my-domain.auth0.com');
    $this->config->setClientSecret($this->secret);
    $this->config->setAudience(['https://example.com/health-api']);
    $this->config->setTokenAlgorithm(Token::ALGO_HS256);
    $this->config->setStrategy(SdkConfiguration::STRATEGY_API);

    $this->token = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => "https://my-domain.auth0.com/",
        "sub" => "auth0|123456",
        "aud" => [
          "https://example.com/health-api",
          "https://my-domain.auth0.com/userinfo"
        ],
        "azp" => "my_client_id",
        "exp" => time() + 60,
        "iat" => time(),
        "scope" => "openid profile read:patients read:admin"
    ]);
    $this->bearerToken = ['Authorization' => 'Bearer ' . $this->token->toString()];

    $this->route = '/' . uniqid();
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
    $this->config->setAudience(['BAD_AUDIENCE']);

    expect($this->guard)
        ->user()->toBeNull();

    getJson($this->route, $this->bearerToken)
        ->assertStatus(Response::HTTP_UNAUTHORIZED);

    expect($this->guard)
        ->user()->toBeNull();
});
