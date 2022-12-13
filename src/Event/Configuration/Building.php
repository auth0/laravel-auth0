<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Configuration;

final class Building extends \Auth0\Laravel\Event\Auth0Event implements \Auth0\Laravel\Contract\Event\Configuration\Building
{
    /**
     * {@inheritdoc}
     */
    public function __construct(private array $configuration)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(array $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }
}
