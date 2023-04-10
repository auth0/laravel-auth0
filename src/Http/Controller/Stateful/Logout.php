<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Contract\Auth\Guard as GuardContract;
use Auth0\Laravel\Contract\Entities\Credential;
use Auth0\Laravel\Contract\Http\Controller\Stateful\Logout as LogoutContract;
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

        if (! $guard instanceof GuardContract) {
            return redirect()->intended(config('auth0.routes.home', '/'));
        }

        $loggedIn = $guard->check() ? true : $guard->find(Guard::SOURCE_SESSION) instanceof Credential;

        if ($loggedIn) {
            session()->invalidate();
            $guard->logout();

            $route = config('auth0.routes.home');
            $route = is_string($route) ? $route : '/';
            $route = (string) url($route); /** @phpstan-ignore-line */
            $url   = $this->getSdk()->authentication()->getLogoutLink($route);

            return redirect()->away($url);
        }

        return redirect()->intended(config('auth0.routes.home', '/'));
    }
}
