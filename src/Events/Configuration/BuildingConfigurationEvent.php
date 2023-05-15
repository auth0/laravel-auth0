<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Configuration;

use Auth0\Laravel\Events\EventAbstract;

/**
 * Event fired when the configuration array is being built.
 *
 * @api
 */
class BuildingConfigurationEvent extends EventAbstract implements BuildingConfigurationContract
{
    public function __construct(private array $configuration)
    {
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }
}
