<?php

declare(strict_types=1);

namespace Auth0\Laravel\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base controller for SDK controllers.
 *
 * @codeCoverageIgnore
 *
 * @api
 */
abstract class ControllerAbstract extends Controller
{
    abstract public function __invoke(
        Request $request,
    ): Response;
}
