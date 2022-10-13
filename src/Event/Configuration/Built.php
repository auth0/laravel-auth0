<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Configuration;

use Auth0\SDK\Configuration\SdkConfiguration as Configuration;

final class Built extends \Auth0\Laravel\Event\Auth0Event implements \Auth0\Laravel\Contract\Event\Configuration\Built
{
    /**
     * Whether or not $exception will be thrown.
     */
    private Configuration $configuration;

    /**
     * {@inheritdoc}
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfiguration(Configuration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }
}
