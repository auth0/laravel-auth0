<?php

declare(strict_types=1);

namespace Auth0\Laravel\Http\Controller\Stateful;

use Auth0\Laravel\Auth\Guard;
use Auth0\Laravel\Contract\Auth\Guard as GuardContract;
use Auth0\Laravel\Contract\Entities\Credential;
use Auth0\Laravel\Contract\Http\Controller\Stateful\Login as LoginContract;
use Auth0\Laravel\Http\Controller\ControllerAbstract;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class Login extends ControllerAbstract implements LoginContract
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

        if (! $guard instanceof GuardContract) {
            return redirect()->intended(config('auth0.routes.home', '/'));
        }

        $loggedIn = $guard->check() ? true : $guard->find(Guard::SOURCE_SESSION) instanceof Credential;

        if ($loggedIn) {
            return redirect()->intended(config('auth0.routes.home', '/'));
        }

        $url = $this->getSdk()->login();

        return redirect()->away($url);
    }
}
