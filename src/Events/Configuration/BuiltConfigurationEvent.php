<?php

declare(strict_types=1);

namespace Auth0\Laravel\Events\Configuration;

/**
 * Dispatched after the Auth0 SDK configuration has been built and initialized.
 *
 * @api
 */
final class BuiltConfigurationEvent extends BuiltConfigurationEventAbstract implements BuiltConfigurationEventContract
{
}
