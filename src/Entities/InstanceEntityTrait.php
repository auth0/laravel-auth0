<?php

declare(strict_types=1);

namespace Auth0\Laravel\Entities;

use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Contract\Auth0Interface;

use function is_array;

/**
 * @api
 */
trait InstanceEntityTrait
{
    public function reset(): self
    {
        unset($this->sdk, $this->configuration);

        $this->sdk = null;
        $this->configuration = null;

        return $this;
    }

    /**
     * @param null|array<string>|SdkConfiguration $configuration
     */
    public function setConfiguration(
        SdkConfiguration | array | null $configuration = null,
    ): self {
        if (is_array($configuration)) {
            $configuration = $this->createConfiguration($configuration);
        }

        $this->configuration = $configuration;

        if ($this->configuration instanceof \Auth0\SDK\Configuration\SdkConfiguration && $this->sdk instanceof Auth0Interface) {
            $this->sdk->setConfiguration($this->configuration);
        }

        return $this;
    }

    public static function create(
        SdkConfiguration | array | null $configuration = null,
        ?string $guardConfigurationName = null,
    ): self {
        $instance = new self();

        if (null !== $guardConfigurationName) {
            $instance->setGuardConfigurationKey($guardConfigurationName);
        }

        if (null !== $configuration) {
            $instance->setConfiguration($configuration);
        }

        return $instance;
    }
}
