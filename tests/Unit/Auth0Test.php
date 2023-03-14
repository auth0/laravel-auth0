<?php

declare(strict_types=1);

use Auth0\SDK\Contract\Auth0Interface as SdkContract;
use Auth0\Laravel\Auth0;
use Auth0\SDK\Auth0 as SDKAuth0;
use Auth0\SDK\Configuration\SdkConfiguration;

uses()->group('auth0');

it('can get/set the configuration', function (): void {
    $laravel = app('auth0');
    expect($laravel->getConfiguration())->toBeInstanceOf(SdkConfiguration::class);

    $configuration = new SdkConfiguration(['strategy' => 'none', 'domain' => uniqid() . '.auth0.test']);
    $laravel->setConfiguration($configuration);
    expect($laravel->getConfiguration())->toBe($configuration);

    $domain = uniqid() . '.auth0.test';
    $configuration->setDomain($domain);
    expect($laravel->getConfiguration()->getDomain())->toBe($domain);

    $configuration = new SdkConfiguration(['strategy' => 'none', 'domain' => uniqid() . '.auth0.test']);
    $laravel->setConfiguration($configuration);
    expect($laravel->getConfiguration())->toBe($configuration);

    $sdk = $laravel->getSdk();
    $configuration = new SdkConfiguration(['strategy' => 'none', 'domain' => uniqid() . '.auth0.test']);
    $laravel->setConfiguration($configuration);
    expect($laravel->getConfiguration())->toBe($configuration);
    expect($sdk->configuration())->toBe($configuration);
});

it('can get the sdk credentials', function (): void {
    $laravel = app('auth0');

    expect($laravel->getCredentials())
        ->toBeNull();

    $sdk = $laravel->getSdk();
    $config = $sdk->configuration();
    $session = $config->getSessionStorage();

    $config->setDomain('my-domain.auth0.com');
    $config->setClientId('my_client_id');
    $config->setClientSecret('my_client_secret');
    $config->setCookieSecret('my_cookie_secret');
    $config->setStrategy(SdkConfiguration::STRATEGY_REGULAR);

    $session->set('user', ['sub' => 'hello|world']);
    $session->set('idToken', uniqid());
    $session->set('accessToken', uniqid());
    $session->set('accessTokenScope', [uniqid()]);
    $session->set('accessTokenExpiration', time() - 1000);

    // As we manually set the session values, we need to refresh the SDK state to ensure it's in sync.
    $sdk->refreshState();

    expect($laravel->getCredentials())
        ->toBeObject()
        ->toHaveProperty('accessToken', $session->get('accessToken'))
        ->toHaveProperty('accessTokenScope', $session->get('accessTokenScope'))
        ->toHaveProperty('accessTokenExpiration', $session->get('accessTokenExpiration'))
        ->toHaveProperty('idToken', $session->get('idToken'))
        ->toHaveProperty('user', $session->get('user'));
});

it('can get/set the SDK', function (): void {
    $laravel = app('auth0');
    expect($laravel->getSdk())->toBeInstanceOf(SdkContract::class);

    $sdk = new SDKAuth0(['strategy' => 'none']);
    $laravel->setSdk($sdk);
    expect($laravel->getSdk())->toBeInstanceOf(SdkContract::class);
});

it('can reset the internal static state', function (): void {
    $laravel = app('auth0');
    $cache = spl_object_id($laravel->getSdk());

    unset($laravel); // Force the object to be destroyed. Static state will remain.

    $laravel = app('auth0');
    $updated = spl_object_id($laravel->getSdk());
    expect($cache)->toBe($updated);

    $laravel->reset(); // Reset the static state.

    $laravel = app('auth0');
    $updated = spl_object_id($laravel->getSdk());
    expect($cache)->not->toBe($updated);
});
