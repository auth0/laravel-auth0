<?php

declare(strict_types=1);

uses(\Auth0\Laravel\Tests\TestCase::class)->
    beforeEach(function (): void {
        $this->service = createService();
    }, )->
    in(__DIR__);

function createServiceConfiguration(
    array $configuration = [],
): Auth0\SDK\Configuration\SdkConfiguration {
    $defaults = [
        'strategy' => 'none',
    ];

    return new \Auth0\SDK\Configuration\SdkConfiguration(array_merge($defaults, $configuration));
}

function createService(
    ?Auth0\SDK\Configuration\SdkConfiguration $configuration = null,
): Auth0\Laravel\Auth0 {
    if (null === $configuration) {
        $configuration = createServiceConfiguration();
    }

    return (new \Auth0\Laravel\Auth0())->setConfiguration($configuration);
}

function createSdk(
    ?Auth0\SDK\Configuration\SdkConfiguration $configuration = null,
): Auth0\SDK\Auth0 {
    if (null === $configuration) {
        $configuration = createServiceConfiguration();
    }

    return new \Auth0\SDK\Auth0($configuration);
}
