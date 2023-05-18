<?php

declare(strict_types=1);

namespace Auth0\Laravel\Model;

use Auth0\Laravel\Users\{ImposterUserContract, UserAbstract, UserTrait};

/**
 * @deprecated 7.8.0 Use Auth0\Laravel\Users\ImposterUser instead.
 *
 * @api
 */
final class Imposter extends UserAbstract implements ImposterUserContract
{
    use UserTrait;
}
