<?php

declare(strict_types=1);

namespace Auth0\Laravel\Event\Stateful;

use Auth0\Laravel\Contract\Event\Stateful\TokenRefreshSucceeded as TokenRefreshSucceededContract;
use Auth0\Laravel\Event\Auth0Event;

final class TokenRefreshSucceeded extends Auth0Event implements TokenRefreshSucceededContract
{
}
