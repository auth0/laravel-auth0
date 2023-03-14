<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Contract\Event\Stateful\TokenExpired as TokenExpiredContract;
use Auth0\Laravel\Event\Auth0Event;

final class TokenExpired extends Auth0Event implements TokenExpiredContract
{
}
