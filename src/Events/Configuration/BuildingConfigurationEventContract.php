<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Configuration;

use Auth0\Laravel\Events\EventContract;

/**
 * @api
 */
interface BuildingConfigurationEventContract extends EventContract
{
    /**
     * @return array{configuration: array<array-key, mixed>}
     */
    public function jsonSerialize(): array;
}
