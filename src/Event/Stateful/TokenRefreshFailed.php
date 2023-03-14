<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Contract\Event\Stateful\TokenRefreshFailed as TokenRefreshFailedContract;
use Auth0\Laravel\Event\Auth0Event;

final class TokenRefreshFailed extends Auth0Event implements TokenRefreshFailedContract
{
}
