<?php

declare(strict_types=1);

use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Support\Facades\Route;
use Auth0\Laravel\Http\Controller\Stateful\Logout;

uses()->group('stateful', 'controller', 'controller.stateful', 'controller.stateful.logout');

beforeEach(function (): void {
    $this->laravel = app('auth0');
    $this->guard = auth('testGuard');
    $this->sdk = $this->laravel->getSdk();
    $this->config = $this->sdk->configuration();
    $this->session = $this->config->getSessionStorage();
    $this->transient = $this->config->getTransientStorage();

    $this->secret = uniqid();

    $this->config->setDomain('my-domain.auth0.com');
    $this->config->setClientId('my_client_id');
    $this->config->setClientSecret($this->secret);
    $this->config->setCookieSecret('my_cookie_secret');
    $this->config->setStrategy(SdkConfiguration::STRATEGY_REGULAR);

    $this->templates['validSession'] = [
        'auth0_session_user' => ['sub' => 'hello|world'],
        'auth0_session_idToken' => uniqid(),
        'auth0_session_accessToken' => uniqid(),
        'auth0_session_accessTokenScope' => [uniqid()],
        'auth0_session_accessTokenExpiration' => time() + 60,
    ];

    Route::get('/logout', Logout::class);
});

it('redirects to the home route if an incompatible guard is active', function (): void {
    config($config = [
        'auth.defaults.guard' => 'web',
        'auth.guards.testGuard' => null,
        'auth0.routes.home' => '/' . uniqid(),
    ]);

    $this->get('/logout')
         ->assertRedirect($config['auth0.routes.home']);
});

it('redirects to the home route when a user is not already logged in', function (): void {
    config($config = [
        'auth0.routes.home' => '/' . uniqid(),
    ]);

    $this->get('/logout')
         ->assertRedirect($config['auth0.routes.home']);
});

it('redirects to the Auth0 logout endpoint', function (): void {
    $this->withSession($this->templates['validSession'])
         ->get('/logout')
            ->assertRedirectContains('/v2/logout');
});
