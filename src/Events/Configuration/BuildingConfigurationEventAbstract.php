<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Configuration;

use Auth0\Laravel\Events\EventAbstract;

/**
 * @internal
 *
 * @api
 */
abstract class BuildingConfigurationEventAbstract extends EventAbstract
{
    /**
     * @param array<mixed> $configuration a configuration array for use with the Auth0-PHP SDK
     */
    public function __construct(
        public array &$configuration,
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
