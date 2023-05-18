<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Configuration;

/**
 * Event fired when the configuration array has been built.
 *
 * @api
 */
final class BuiltConfigurationEvent extends BuiltConfigurationEventAbstract implements BuiltConfigurationEventContract
{
}
