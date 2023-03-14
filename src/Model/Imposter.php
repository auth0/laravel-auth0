<?php

declare(strict_types=1);

namespace Auth0\Laravel\Model;

use Auth0\Laravel\Contract\Model\Stateful\User as UserContract;

final class Imposter extends User implements UserContract
{
}
