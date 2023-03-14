<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Exception;

use Auth0\SDK\Exception\Auth0Exception;

interface GuardException extends Auth0Exception
{
    public const USER_MODEL_NORMALIZATION_FAILURE = 'Unable to convert user to array. Class should implement JsonSerializable, Arrayable or Jsonable.';
    public const USER_PROVIDER_UNAVAILABLE        = 'Unable to create User Provider %s from configuration.';
    public const USER_PROVIDER_UNCONFIGURED       = 'There is no User Provider configured. Please ensure the `provider` key is set in the Guard configuration, and points to a valid entry in the `providers` configuration.';
}
