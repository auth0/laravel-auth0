<?php namespace Auth0\Login\Middleware;


use Auth0\Login\Contract\Auth0UserRepository;
use Auth0\SDK\Exception\CoreException;

class Auth0JWTMiddleware {

    protected $userRepository;

    public function __construct(Auth0UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    protected function getToken($request) {
        // Get the encrypted user JWT
        $authorizationHeader = $request->header("Authorization");
        return trim(str_replace('Bearer ', '', $authorizationHeader));
    }

    protected function validateToken($token) {
        return ($token !== '');
    }

    public function handle($request, \Closure $next)
    {
        $auth0 = \App::make('auth0');

        $token = $this->getToken($request);
        
        if ( ! $this->validateToken($token)) {
            return \Response::make("Unauthorized user", 401);
        }

        if ($token) {
            try {
                $jwtUser = $auth0->decodeJWT($token);
            }
            catch(CoreException $e) {
                return \Response::make("Unauthorized user", 401);
            }
            catch(InvalidTokenException $e) {
                return \Response::make("Unauthorized user", 401);
            }

            // if it does not represent a valid user, return a HTTP 401
            $user = $this->userRepository->getUserByDecodedJWT($jwtUser);

            if (!$user) {
                return \Response::make("Unauthorized user", 401);
            }

            // lets log the user in so it is accessible
            \Auth::login($user);
        }

        // continue the execution
        return $next($request);
    }

}