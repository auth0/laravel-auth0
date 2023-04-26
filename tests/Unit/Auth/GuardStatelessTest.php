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
    $this->guard = auth('testGuard');
    $this->sdk = $this->laravel->getSdk();
    $this->config = $this->sdk->configuration();

    $this->token = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => 'https://' . config('auth0.domain') . '/',
        "sub" => "auth0|123456",
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
    $this->config->setAudience(['BAD_AUDIENCE']);

    expect($this->guard)
        ->user()->toBeNull();

    getJson($this->route, $this->bearerToken)
        ->assertStatus(Response::HTTP_UNAUTHORIZED);

    expect($this->guard)
        ->user()->toBeNull();
});
