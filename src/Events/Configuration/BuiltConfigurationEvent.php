<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Configuration;

use Auth0\Laravel\Events\EventAbstract;
use Auth0\SDK\Configuration\SdkConfiguration;

/**
 * Event fired when the configuration array has been built.
 *
 * @api
 */
class BuiltConfigurationEvent extends EventAbstract implements BuiltConfigurationContract
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
