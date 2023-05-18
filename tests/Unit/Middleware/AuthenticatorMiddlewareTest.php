<?php

declare(strict_types=1);

use Auth0\Laravel\ServiceProvider;
use Auth0\SDK\Configuration\SdkConfiguration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

uses()->group('Middleware/AuthenticatorMiddleware');

beforeEach(function (): void {
    $this->secret = uniqid();

    config([
        'auth0.AUTH0_CONFIG_VERSION' => 2,
        'auth0.guards.default.strategy' => SdkConfiguration::STRATEGY_NONE,
    ]);
});

it('installs the Auth0 Authenticator', function (): void {
    config(['auth.defaults.guard' => 'web']);
    app(ServiceProvider::class, ['app' => app()])->registerMiddleware(app('router'));

    Route::middleware('web')->get('/test', function (): JsonResponse {
        if (auth()->guard()->name !== 'auth0-session') {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'guard' => auth()->guard()->name,
            'middleware' => app('router')->getMiddlewareGroups()
        ]);
    });

    $this->get('/test')
         ->assertOK();
});
