<?php

declare(strict_types=1);

test('the service is created successfully', function() {
    expect($this->service)
        ->toBeInstanceOf(\Auth0\Laravel\Auth0::class);
});

test('the service instantiates it\'s own configuration if none is assigned', function() {
    $service =  new \Auth0\Laravel\Auth0;

    expect($service->getConfiguration())
        ->toBeInstanceOf(\Auth0\SDK\Configuration\SdkConfiguration::class);
})->throws(\Auth0\SDK\Exception\ConfigurationException::class);

test('the service\'s getSdk() method returns an Auth0 SDK instance', function() {
    expect($this->service->getSdk())
        ->toBeInstanceOf(\Auth0\SDK\Auth0::class);
});

test('the service\'s getConfiguration method returns an SdkConfiguration instance', function() {
    expect($this->service->getConfiguration())
        ->toBeInstanceOf(\Auth0\SDK\Configuration\SdkConfiguration::class);
});

test('the service\'s getState method returns a StateInstance instance', function() {
    expect($this->service->getState())
        ->toBeInstanceOf(\Auth0\Laravel\StateInstance::class);
});

test('the service\'s setSdk() method allows overwriting the Auth0 instance', function() {
    $oldSdk = $this->service->getSdk();
    $newSdk = createSdk();

    $this->service->setSdk($newSdk);

    expect($this->service->getSdk())
        ->toBe($newSdk)
        ->not()
            ->toBe($oldSdk);
});

test('the service\'s setConfiguration() method allows overwriting the SdkConfiguration instance', function() {
    $oldConfiguration = $this->service->getConfiguration();
    $newConfiguration = createServiceConfiguration();

    $this->service->setConfiguration($newConfiguration);

    expect($this->service->getConfiguration())
        ->toBe($newConfiguration)
        ->not()
            ->toBe($oldConfiguration);
});
