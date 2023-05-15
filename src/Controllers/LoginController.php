<?php

declare(strict_types=1);

namespace Auth0\Laravel\Controllers;

use Auth0\Laravel\Entities\CredentialEntityContract;
use Auth0\Laravel\Events\LoginAttempting;
use Auth0\Laravel\Exceptions\ControllerException;
use Auth0\Laravel\Guards\AuthenticationGuardContract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Controller for handling a login request.
 *
 * @api
 */
final class LoginController extends ControllerAbstract implements LoginControllerContract
{
    /**
     * @phpcsSuppress SlevomatCodingStandard.Functions.UnusedParameter
     *
     * @param Request $request
     */
    public function __invoke(
        Request $request,
    ): Response {
        $guard = auth()->guard();

        if (! $guard instanceof AuthenticationGuardContract) {
            logger()->error(sprintf('A request implementing the `%s` controller was not routed through a Guard configured with an Auth0 driver. The incorrectly assigned Guard was: %s', self::class, $guard::class), $request->toArray());

            throw new ControllerException(ControllerException::ROUTED_USING_INCOMPATIBLE_GUARD);
        }

        if ($guard->check() ? true : $guard->find(AuthenticationGuardContract::SOURCE_SESSION) instanceof CredentialEntityContract) {
            return redirect()->intended(config('auth0.routes.home', '/'));
        }

        $event = new LoginAttempting();
        event($event);

        $url = $guard->sdk()->login(
            params: $event->getParameters(),
        );

        return redirect()->away($url);
    }
}
