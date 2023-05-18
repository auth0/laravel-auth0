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
abstract class BuiltConfigurationEventAbstract extends EventAbstract
{
    public function __construct(private SdkConfiguration $configuration)
    {
    }

    final public function getConfiguration(): SdkConfiguration
    {
        return $this->configuration;
    }

    final public function setConfiguration(SdkConfiguration $configuration): void
    {
        $this->configuration = $configuration;
    }
}
