<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Configuration;

use Auth0\Laravel\Events\EventAbstract;
use Auth0\SDK\Configuration\SdkConfiguration;

/**
 * @internal
 *
 * @api
 */
abstract class BuiltConfigurationEventAbstract extends EventAbstract
{
    /**
     * @param SdkConfiguration $configuration an instance of SdkConfiguration for use with the Auth0-PHP SDK
     */
    public function __construct(
        public SdkConfiguration &$configuration,
    ) {
    }

    /**
     * @psalm-suppress LessSpecificImplementedReturnType
     *
     * @return array{configuration: mixed}
     */
    final public function jsonSerialize(): array
    {
        return [
            'configuration' => json_decode(json_encode($this->configuration, JSON_THROW_ON_ERROR), true),
        ];
    }
}
