<?php namespace Auth0\Login\Middleware;

class ForceAuthMiddleware {

    public function handle($request, \Closure $next)
    {
        if ( ! \Auth::check()) {
            return \Response::make("Unauthorized user", 401);
        }

        return $next($request);
    }

}