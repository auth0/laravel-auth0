<?php namespace Auth0\Login\Middleware;


use Auth0\Login\Contract\Auth0UserRepository;
use Illuminate\Contracts\Routing\Middleware;

class Auth0JWTMiddleware implements Middleware {

    protected $userRepository;

    public function __construct(Auth0UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function handle($request, \Closure $next)
    {
        $auth0 = \App::make('auth0');

        // Get the encrypted user JWT
        $authorizationHeader = $request->header("Authorization");
        $encUser = str_replace('Bearer ', '', $authorizationHeader);

        if (trim($encUser) != '') {
            $canDecode = $auth0->decodeJWT($encUser);
        } else {
            $canDecode = false;
        }

        // if it is not valid, return a HTTP 401
        if (!$canDecode) {
            return \Response::make("Unauthorized user", 401);
        }

        // if it does not represent a valid user, return a HTTP 401
        $user = $this->userRepository->getUserByDecodedJWT($auth0->jwtuser());

        if (!$user) {
            return \Response::make("Unauthorized user", 401);
        }

        // lets log the user in so it is accesible
        \Auth::login($user);

        // continue the excecution
        return $next($request);
    }

}