<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Configuration;

use Auth0\Laravel\Contract\Event\Configuration\Building as BuildingContract;
use Auth0\Laravel\Event\Auth0Event;

final class Building extends Auth0Event implements BuildingContract
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
