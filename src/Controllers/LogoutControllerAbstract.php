<?php

declare(strict_types=1);

namespace Auth0\Laravel\Controllers;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Configuration;
use Auth0\Laravel\Entities\CredentialEntityContract;
use Auth0\Laravel\Exceptions\ControllerException;
use Auth0\Laravel\Guards\GuardAbstract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for handling a logout request.
 *
 * @api
 */
abstract class LogoutControllerAbstract extends ControllerAbstract
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

        if (! $guard instanceof GuardAbstract) {
            logger()->error(sprintf('A request implementing the `%s` controller was not routed through a Guard configured with an Auth0 driver. The incorrectly assigned Guard was: %s', self::class, $guard::class), $request->toArray());

            throw new ControllerException(ControllerException::ROUTED_USING_INCOMPATIBLE_GUARD);
        }

        $loggedIn = $guard->check() ? true : null;
        $loggedIn ??= (($guard instanceof Guard) ? $guard->find(Guard::SOURCE_SESSION) : $guard->find()) instanceof CredentialEntityContract;

        $landing = Configuration::string(Configuration::CONFIG_NAMESPACE_ROUTES . Configuration::CONFIG_ROUTE_AFTER_LOGOUT);
        $landing ??= Configuration::string(Configuration::CONFIG_NAMESPACE_ROUTES . Configuration::CONFIG_ROUTE_INDEX);
        $landing ??= '/';

        if ($loggedIn) {
            session()->invalidate();

            $guard->logout(); /** @phpstan-ignore-line */
            $route = (string) url($landing); /** @phpstan-ignore-line */
            $url = $guard->sdk()->authentication()->getLogoutLink($route);

            return redirect()->away($url);
        }

        return redirect()->intended($landing);
    }
}
