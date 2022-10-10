<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Configuration;

use Auth0\SDK\Configuration\SdkConfiguration;

final class Built extends \Auth0\Laravel\Event\Auth0Event implements \Auth0\Laravel\Contract\Event\Configuration\Built
{
    /**
     * Whether or not $exception will be thrown.
     */
    private SdkConfiguration $configuration;

    /**
     * @inheritdoc
     */
    public function __construct(
        SdkConfiguration $configuration
    ) {
        $this->configuration = $configuration;
    }

    /**
     * @inheritdoc
     */
    public function setConfiguration(
        SdkConfiguration $configuration
    ): self {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getConfiguration(): SdkConfiguration
    {
        return $this->configuration;
    }
}
