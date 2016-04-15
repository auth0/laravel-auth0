<?php namespace Auth0\Login\Middleware;


use Auth0\Login\Contract\Auth0UserRepository;
use Auth0\SDK\Exception\CoreException;

class Auth0JWTMiddleware {

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

        if (trim($encUser) == '') {
            return \Response::make("Unauthorized user", 401);
        }

        try {
            $jwtUser = $auth0->decodeJWT($encUser);
        }
        catch(CoreException $e) {
            return \Response::make("Unauthorized user", 401);
        }
        catch(Exception $e) {
            echo $e;exit;
        }

        // if it does not represent a valid user, return a HTTP 401
        $user = $this->userRepository->getUserByDecodedJWT($jwtUser);

        if (!$user) {
            return \Response::make("Unauthorized user", 401);
        }

        // lets log the user in so it is accessible
        \Auth::login($user);

        // continue the execution
        return $next($request);
    }

}
