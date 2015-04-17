<?php namespace Auth0\Login\Middleware;


use Illuminate\Contracts\Routing\Middleware;

class Auth0JWTMiddleware implements Middleware {

    public function handle($request, \Closure $next)
    {
        // Get the encrypted user
        $authorizationHeader = $request->header("Authorization");
        $encUser = str_replace('Bearer ', '', $authorizationHeader);
        if (trim($encUser) != '') {
            $canDecode = \App::make('auth0')->decodeJWT($encUser);
        } else {
            $canDecode = false;
        }

        if (!$canDecode) {
            return \Response::make("Unauthorized user", 401);
        }

        return $next($request);
    }

}