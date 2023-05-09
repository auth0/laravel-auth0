<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Contract\Auth\Guards\SessionGuardContract;
use Auth0\Laravel\Contract\Entities\CredentialContract;
use Auth0\Laravel\Contract\Http\Controller\Stateful\Logout as LogoutContract;
use Auth0\Laravel\Exception\ControllerException;
use Auth0\Laravel\Http\Controller\ControllerAbstract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use function is_string;

final class Logout extends ControllerAbstract implements LogoutContract
{
    /**
     * @psalm-suppress RedundantCastGivenDocblockType
     *
     * @param Request $request
     */
    public function __invoke(
        Request $request,
    ): Response {
        $guard = auth()->guard();

        if (! $guard instanceof SessionGuardContract) {
            logger()->error(sprintf('A request implementing the `%s` controller was not routed through a Guard configured with an Auth0 driver. The incorrectly assigned Guard was: %s', self::class, $guard::class), $request->toArray());
            throw new ControllerException(ControllerException::ROUTED_USING_INCOMPATIBLE_GUARD);
        }

        if ($guard->check() ? true : $guard->find(Guard::SOURCE_SESSION) instanceof CredentialContract) {
            session()->invalidate();
            $guard->logout();

            $route = config('auth0.routes.home');
            $route = is_string($route) ? $route : '/';
            $route = (string) url($route); /** @phpstan-ignore-line */
            $url = $guard->sdk()->authentication()->getLogoutLink($route);

            return redirect()->away($url);
        }

        return redirect()->intended(config('auth0.routes.home', '/'));
    }
}
