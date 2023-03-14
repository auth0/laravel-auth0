<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Configuration;

use Auth0\Laravel\Contract\Event\Configuration\Built as BuiltContract;
use Auth0\Laravel\Event\Auth0Event;
use Auth0\SDK\Configuration\SdkConfiguration as Configuration;

final class Built extends Auth0Event implements BuiltContract
{
    public function __construct(private Configuration $configuration)
    {
    }

    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }

    public function setConfiguration(Configuration $configuration): self
    {
        $this->configuration = $configuration;

        return $this;
    }
}
