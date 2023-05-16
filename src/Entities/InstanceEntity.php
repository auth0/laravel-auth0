<?php

declare(strict_types=1);

namespace Auth0\Laravel\Entities;

/**
 * An entity representing an instance of the Auth0 PHP SDK.
 *
 * @internal
 *
 * @api
 */
final class InstanceEntity extends InstanceEntityAbstract implements InstanceEntityContract
{
    use InstanceEntityTrait;
}
