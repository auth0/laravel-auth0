<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Configuration;

use Auth0\Laravel\Events\Configuration\BuiltConfigurationEvent;
use Auth0\SDK\Configuration\SdkConfiguration;

/**
 * Event fired when the configuration array has been built.
 *
 * @codeCoverageIgnore
 * @deprecated 7.8.0 Use Auth0\Laravel\Events\Configuration\BuiltConfigurationEvent instead.
 * @api
 */
final class Built extends BuiltConfigurationEvent
{
    public function __construct(private SdkConfiguration $configuration)
    {
    }

    public function getConfiguration(): SdkConfiguration
    {
        return $this->configuration;
    }

    public function setConfiguration(SdkConfiguration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }
}
