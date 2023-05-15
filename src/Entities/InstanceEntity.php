<?php

declare(strict_types=1);

namespace Auth0\Laravel\Entities;

use Auth0\SDK\Configuration\SdkConfiguration;

/**
 * An entity representing an instance of the Auth0 PHP SDK.
 *
 * @internal
 * @api
 */
final class InstanceEntity extends InstanceEntityAbstract
{
    final public static function create(
        SdkConfiguration | array | null $configuration = null,
    ): self {
        $instance = new self();
        $instance->setConfiguration($configuration);

        return $instance;
    }
}
