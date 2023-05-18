<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Configuration;

use Auth0\Laravel\Events\EventAbstract;

/**
 * @internal
 *
 * @api
 */
abstract class BuildingConfigurationEventAbstract extends EventAbstract
{
    public function __construct(private array $configuration)
    {
    }

    final public function getConfiguration(): array
    {
        return $this->configuration;
    }

    final public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }
}
