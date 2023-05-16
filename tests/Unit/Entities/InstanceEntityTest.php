<?php

declare(strict_types=1);

use Auth0\Laravel\Entities\InstanceEntity;
use Auth0\SDK\Exception\ConfigurationException;

uses()->group('Entities/InstanceEntity');

beforeEach(function (): void {
});

it('instantiates an empty configuration if a non-array is supplied', function (): void {
    config(['auth0' => true]);

    (new InstanceEntity())->getConfiguration();
})->throws(ConfigurationException::class);
