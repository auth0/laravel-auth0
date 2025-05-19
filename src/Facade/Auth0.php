<?php

declare(strict_types=1);

namespace Auth0\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Facade for the Auth0 SDK.
 *
 * @method static \Auth0\SDK\Configuration\SdkConfiguration getConfiguration()
 * @method static null|object getCredentials()
 * @method static null|string getGuardConfigurationKey()
 * @method static \Auth0\SDK\Contract\Auth0Interface getSdk()
 * @method static \Auth0\SDK\Contract\API\ManagementInterface management()
 * @method static self setGuardConfigurationKey(null|string $guardConfigurationKey = null)
 * @method static \Auth0\SDK\Contract\Auth0Interface setSdk(\Auth0\SDK\Contract\Auth0Interface $sdk)
 * @method static self reset()
 * @method static self setConfiguration(\Auth0\SDK\Configuration\SdkConfiguration|array|null $configuration = null)
 *
 * @see \Auth0\Laravel\Service
 * @see \Auth0\Laravel\ServiceAbstract
 * @see \Auth0\Laravel\Entities\InstanceEntityAbstract
 * @see \Auth0\Laravel\Entities\InstanceEntityTrait
 *
 * @codeCoverageIgnore
 *
 * @api
 */
final class Auth0 extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'auth0';
    }
}
