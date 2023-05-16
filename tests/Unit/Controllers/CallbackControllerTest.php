<?php

declare(strict_types=1);

use Auth0\Laravel\Controllers\CallbackController;
use Auth0\Laravel\Events\AuthenticationFailed;
use Auth0\Laravel\Events\AuthenticationSucceeded;
use Auth0\Laravel\Exceptions\ControllerException;
use Auth0\Laravel\Exceptions\Controllers\CallbackControllerException;
use Auth0\Laravel\Users\ImposterUser;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Exception\StateException;
use Auth0\SDK\Token;
use Auth0\SDK\Token\Generator;
use Illuminate\Support\Facades\Route;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Response;

use function Pest\Laravel\getJson;

uses()->group('stateful', 'controller', 'controller.stateful', 'controller.stateful.callback');

beforeEach(function (): void {
    $this->secret = uniqid();

    config([
        'auth0.default.strategy' => SdkConfiguration::STRATEGY_REGULAR,
        'auth0.default.domain' => uniqid() . '.auth0.com',
        'auth0.default.clientId' => uniqid(),
        'auth0.default.clientSecret' => $this->secret,
        'auth0.default.cookieSecret' => uniqid(),
    ]);

    $this->guard = auth('legacyGuard');
    $this->sdk = $this->guard->sdk();
    $this->config = $this->guard->sdk()->configuration();
    $this->user = new ImposterUser(['sub' => uniqid('auth0|')]);

    Route::get('/auth0/callback', CallbackController::class)->name('callback');
});

it('redirects home if an incompatible guard is active', function (): void {
    config([
        'auth.defaults.guard' => 'web',
        'auth.guards.legacyGuard' => null,
    ]);

    expect(function () {
        $this->withoutExceptionHandling()
             ->getJson('/auth0/callback')
             ->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
    })->toThrow(ControllerException::class);
});

it('accepts code and state parameters', function (): void {
    expect(function () {
        $this->withoutExceptionHandling()
             ->getJson('/auth0/callback?code=code&state=state');
    })->toThrow(StateException::class);

    $this->assertDispatched(Attempting::class, 1);
    $this->assertDispatched(Failed::class, 1);
    $this->assertDispatched(AuthenticationFailed::class, 1);

    $this->assertDispatchedOrdered([
        Attempting::class,
        Failed::class,
        AuthenticationFailed::class,
    ]);
});

it('accepts error and error_description parameters', function (): void {
    expect(function () {
        $this->withoutExceptionHandling()
             ->getJson('/auth0/callback?error=123&error_description=456');
    })->toThrow(CallbackControllerException::class);

    $this->assertDispatched(Attempting::class, 1);
    $this->assertDispatched(Failed::class, 1);
    $this->assertDispatched(AuthenticationFailed::class, 1);

    $this->assertDispatchedOrdered([
        Attempting::class,
        Failed::class,
        AuthenticationFailed::class,
    ]);
});

it('returns a user and sets up a session', function (): void {
    $this->config->setTokenAlgorithm(Token::ALGO_HS256);

    $state = uniqid();
    $pkce = uniqid();
    $nonce = uniqid();
    $verifier = uniqid();

    $accessToken = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => 'https://' . config('auth0.default.domain') . '/',
        'sub' => 'hello|world',
        'aud' => config('auth0.default.clientId'),
        'exp' => time() + 60,
        'iat' => time(),
        'email' => 'john.doe@somewhere.test'
    ], []);

    $idToken = Generator::create($this->secret, Token::ALGO_HS256, [
        "iss" => 'https://' . config('auth0.default.domain') . '/',
        'sub' => 'hello|world',
        'aud' => config('auth0.default.clientId'),
        'iat' => time(),
        'exp' => time() + 60,
        'azp' => config('auth0.default.clientId'),
        'scope' => 'openid profile email',
        'nonce' => $nonce,
    ], []);

    $factory = $this->config->getHttpResponseFactory();
    $response = $factory->createResponse();
    $response->getBody()->write(json_encode([
        'access_token' => $accessToken->toString(),
        'id_token' => $idToken->toString(),
        'scope' => 'openid profile email',
        'expires_in' => 60,
    ]));

    $client = $this->config->getHttpClient();
    $client->addResponse('POST', 'https://' . config('auth0.default.domain') . '/oauth/token', $response);

    $this->withSession([
        'auth0_transient_state' => $state,
        'auth0_transient_pkce' => $pkce,
        'auth0_transient_nonce' => $nonce,
        'auth0_transient_code_verifier' => $verifier
    ])->getJson('/auth0/callback?code=code&state=' . $state)
        ->assertFound()
        ->assertLocation('/');

    $this->assertDispatched(Attempting::class, 1);
    $this->assertDispatched(Validated::class, 1);
    $this->assertDispatched(Login::class, 1);
    $this->assertDispatched(AuthenticationSucceeded::class, 1);
    $this->assertDispatched(Authenticated::class, 1);

    $this->assertDispatchedOrdered([
        Attempting::class,
        Validated::class,
        Login::class,
        AuthenticationSucceeded::class,
        Authenticated::class,
    ]);
});

it('redirects visitors if an expected parameter is not provided', function (): void {
    $this->getJson('/auth0/callback?code=code')
         ->assertFound()
         ->assertLocation('/login');
});
