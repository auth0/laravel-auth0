<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Entities\InstanceEntityTrait;

/**
 * Auth0 Laravel SDK service provider. Provides access to the SDK's methods.
 *
 * @method static \Auth0\SDK\Configuration\SdkConfiguration   getConfiguration()
 * @method static null|object                                 getCredentials()
 * @method static null|string                                 getGuardConfigurationKey()
 * @method static \Auth0\SDK\Contract\Auth0Interface          getSdk()
 * @method static \Auth0\SDK\Contract\API\ManagementInterface management()
 * @method static self                                        setGuardConfigurationKey(null|string $guardConfigurationKey = null)
 * @method static \Auth0\SDK\Contract\Auth0Interface          setSdk(\Auth0\SDK\Contract\Auth0Interface $sdk)
 * @method static self                                        reset()
 * @method static self                                        setConfiguration(\Auth0\SDK\Configuration\SdkConfiguration|array|null $configuration = null)
 *
 * @see Service
 * @see ServiceAbstract
 * @see Entities\InstanceEntityAbstract
 * @see InstanceEntityTrait
 *
 * @codeCoverageIgnore
 *
 * @deprecated 7.8.0 Use Auth0\Laravel\Service instead.
 *
 * @api
 */
final class Auth0 extends ServiceAbstract implements ServiceContract
{
    use InstanceEntityTrait;
}
