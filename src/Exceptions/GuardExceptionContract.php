<?php

declare(strict_types=1);

namespace Auth0\Laravel\Exceptions;

/**
 * Exception thrown when an error occurs in the SDK's guards.
 *
 * @codeCoverageIgnore
 *
 * @api
 */
interface GuardExceptionContract extends ExceptionContract
{
    /**
     * @var string
     */
    public const USER_MODEL_NORMALIZATION_FAILURE = 'Unable to convert user to array. Class should implement JsonSerializable, Arrayable or Jsonable.';

    /**
     * @var string
     */
    public const USER_PROVIDER_UNAVAILABLE = 'Unable to create User Provider %s from configuration.';

    /**
     * @var string
     */
    public const USER_PROVIDER_UNCONFIGURED = 'There is no User Provider configured. Please ensure the `provider` key is set in the Guard configuration, and points to a valid entry in the `providers` configuration.';
}
