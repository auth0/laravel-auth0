<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Contract\Auth0 as ServiceContract;
use Auth0\Laravel\Entities\Configuration;
use Auth0\Laravel\Http\Controller\Stateful\{Login, Logout, Callback};
use Illuminate\Support\Facades\Route;
use Psr\Http\Message\ResponseInterface;

/**
 * Service that provides access to the Auth0 SDK.
 */
final class Auth0 extends Configuration implements ServiceContract
{
    /**
     * The Laravel-Auth0 SDK version:.
     *
     * @var string
     */
    public const VERSION = '7.7.0';

    /**
     * Register the SDK's authentication routes and controllers.
     *
     * @param string $authenticationGuard The name of the authentication guard to use.
     */
    public static function routes(
        string $authenticationGuard = 'auth0-session'
    ): void {
        Route::group(['middleware' => ['web', 'guard:' . $authenticationGuard]], static function () : void {
            Route::get('/login', Login::class)->name('login');
            Route::get('/logout', Logout::class)->name('logout');
            Route::get('/callback', Callback::class)->name('callback');
        });
    }

    /**
     * Decode a PSR-7 HTTP Response Message containing a JSON content body to a PHP array. Returns null if the response was not successful, or the response body was not JSON.
     *
     * @return null|array<mixed>
     */
    public static function json(ResponseInterface $response): ?array
    {
        if (! in_array($response->getStatusCode(), [200, 201], true)) {
            return null;
        }

        $json = json_decode((string) $response->getBody(), true);

        if ( ! is_array($json)) {
            return null;
        }

        return $json;
    }
}
