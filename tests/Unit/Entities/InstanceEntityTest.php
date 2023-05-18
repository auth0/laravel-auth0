<?php

declare(strict_types=1);

use Auth0\Laravel\Entities\InstanceEntity;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Exception\ConfigurationException;

uses()->group('Entities/InstanceEntity');

beforeEach(function (): void {
});

it('instantiates an empty configuration if a non-array is supplied', function (): void {
    config(['auth0' => true]);

    (new InstanceEntity())->getConfiguration();
})->throws(ConfigurationException::class);

test('setGuardConfigurationKey() sets the guard configuration key', function (): void {
    $key = uniqid();
    $instance = new InstanceEntity();
    $instance->setGuardConfigurationKey($key);

    expect($instance->getGuardConfigurationKey())
        ->toBe($key);
});

test('setConfiguration sets the configuration using an SdkConfiguration', function (): void {
    $instance = new InstanceEntity();
    $configuration = new SdkConfiguration(['strategy' => 'none', 'domain' => uniqid() . '.auth0.test']);

    $instance->setConfiguration($configuration);

    expect($instance->getConfiguration())
        ->toBe($configuration);
});

test('setConfiguration sets the configuration using an array', function (): void {
    $instance = new InstanceEntity();
    $configuration = ['strategy' => 'none', 'domain' => uniqid() . '.auth0.test'];

    $instance->setConfiguration($configuration);

    expect($instance->getConfiguration())
        ->toBeInstanceOf(SdkConfiguration::class);
});

test('::create() sets the guard configuration key and configuration', function (): void {
    $key = uniqid();
    $configuration = new SdkConfiguration(['strategy' => 'none', 'domain' => uniqid() . '.auth0.test']);
    $instance = InstanceEntity::create(
        configuration: $configuration,
        guardConfigurationName: $key,
    );

    expect($instance->getConfiguration())
        ->toBe($configuration);

    expect($instance->getGuardConfigurationKey())
        ->toBe($key);
});
