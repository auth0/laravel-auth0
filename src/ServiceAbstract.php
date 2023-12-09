<?php

declare(strict_types=1);

namespace Auth0\Laravel;

use Auth0\Laravel\Controllers\{CallbackController, LoginController, LogoutController};
use Auth0\Laravel\Entities\InstanceEntityAbstract;
use Illuminate\Support\Facades\Route;
use Psr\Http\Message\ResponseInterface;

use function in_array;
use function is_array;

/**
 * @api
 */
abstract class ServiceAbstract extends InstanceEntityAbstract
{
    /**
     * The Laravel-Auth0 SDK version:.
     *
     * @var string
     */
    public const VERSION = '7.12.0';

    /**
     * Decode a PSR-7 HTTP Response Message containing a JSON content body to a PHP array. Returns null if the response was not successful, or the response body was not JSON.
     *
     * @param ResponseInterface $response
     *
     * @return null|array<mixed>
     */
    final public static function json(ResponseInterface $response): ?array
    {
        if (! in_array($response->getStatusCode(), [200, 201], true)) {
            return null;
        }

        $json = json_decode((string) $response->getBody(), true);

        if (! is_array($json)) {
            return null;
        }

        return $json;
    }

    /**
     * Register the SDK's authentication routes and controllers.
     *
     * @param string $authenticationGuard The name of the authentication guard to use.
     */
    final public static function routes(
        string $authenticationGuard = 'auth0-session',
    ): void {
        Route::group(['middleware' => ['web', 'guard:' . $authenticationGuard]], static function (): void {
            Route::get(Configuration::string(Configuration::CONFIG_NAMESPACE_ROUTES . Configuration::CONFIG_ROUTE_LOGIN) ?? '/login', LoginController::class)->name('login');
            Route::get(Configuration::string(Configuration::CONFIG_NAMESPACE_ROUTES . Configuration::CONFIG_ROUTE_LOGOUT) ?? '/logout', LogoutController::class)->name('logout');
            Route::get(Configuration::string(Configuration::CONFIG_NAMESPACE_ROUTES . Configuration::CONFIG_ROUTE_CALLBACK) ?? '/callback', CallbackController::class)->name('callback');
        });
    }
}
