<?php

declare(strict_types=1);

use Auth0\Laravel\Event\Stateful\AuthenticationFailed;
use Auth0\Laravel\Event\Stateful\AuthenticationSucceeded;
use Auth0\Laravel\Exception\Stateful\CallbackException;
use Auth0\Laravel\Model\Stateful\User;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Exception\StateException;
use Auth0\SDK\Token;
use Auth0\SDK\Token\Generator;
use Illuminate\Support\Facades\Route;
use Auth0\Laravel\Http\Controller\Stateful\Callback;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Validated;

use function Pest\Laravel\getJson;

uses()->group('stateful', 'controller', 'controller.stateful', 'controller.stateful.callback');

beforeEach(function (): void {
    $this->laravel = app('auth0');
    $this->guard = auth('testGuard');
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

    Route::get('/auth0/callback', Callback::class)->name('callback');
});

it('redirects home if an incompatible guard is active', function (): void {
    config([
        'auth.defaults.guard' => 'web',
        'auth.guards.testGuard' => null,
        'auth0.routes.home' => '/testing'
    ]);

    getJson('/auth0/callback')
        ->assertFound()
        ->assertLocation('/testing');
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
    })->toThrow(CallbackException::class);

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
    config([
        'auth0.routes.home' => '/testing'
    ]);

    $this->config->setTokenAlgorithm(Token::ALGO_HS256);

    $state = uniqid();
    $pkce = uniqid();
    $nonce = uniqid();
    $verifier = uniqid();

    $accessToken = Generator::create($this->secret, Token::ALGO_HS256, [
        'iss' => 'https://my-domain.auth0.com/',
        'sub' => 'hello|world',
        'aud' => 'my_client_id',
        'exp' => time() + 60,
        'iat' => time(),
        'email' => 'john.doe@somewhere.teset'
    ], []);

    $idToken = Generator::create($this->secret, Token::ALGO_HS256, [
        'iss' => 'https://my-domain.auth0.com/',
        'sub' => 'hello|world',
        'aud' => 'my_client_id',
        'iat' => time(),
        'exp' => time() + 60,
        'azp' => 'my_client_id',
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
    $client->addResponse('POST', 'https://my-domain.auth0.com/oauth/token', $response);

    $this->withSession([
            'auth0_transient_state' => $state,
            'auth0_transient_pkce' => $pkce,
            'auth0_transient_nonce' => $nonce,
            'auth0_transient_code_verifier' => $verifier
         ])->getJson('/auth0/callback?code=code&state=' . $state)
            ->assertFound()
            ->assertLocation('/testing');

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
