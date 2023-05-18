<?php

declare(strict_types=1);

use Auth0\Laravel\Users\StatefulUser;
use Auth0\Laravel\Users\StatelessUser;

uses()->group('UserRepository');

it('returns a stateful user model from session queries', function (): void {
    $repository = $this->app['auth0.repository'];

    expect($repository->fromSession(['name' => 'Stateful']))
        ->toBeInstanceOf(StatefulUser::class)
        ->name->toBe('Stateful');
});

it('returns a stateless user model from access token queries', function (): void {
    $repository = $this->app['auth0.repository'];

    expect($repository->fromAccessToken(['name' => 'Stateless']))
        ->toBeInstanceOf(StatelessUser::class)
        ->name->toBe('Stateless');
});
