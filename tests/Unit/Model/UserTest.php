<?php

declare(strict_types=1);

use Auth0\Laravel\Model\Stateless\User;

uses()->group('stateful', 'model', 'model.user');

it('fills attributes provided to the constructor', function (): void {
    $user = new User(['testing' => 'testing']);

    expect($user->testing)
        ->toBe('testing');
});

it('fills attributes', function (): void {
    $user = new User();
    $user->fill(['testing' => 'testing']);

    expect($user->testing)
        ->toBe('testing');
});

it('sets attributes with magic', function (): void {
    $user = new User();
    $user->testing = 'testing';

    expect($user->testing)
        ->toBe('testing');
});

it('sets attributes', function (): void {
    $user = new User();
    $user->setAttribute('testing', 'testing');

    expect($user->getAttribute('testing'))
        ->toBe('testing');
});

it('gets attributes array', function (): void {
    $user = new User([
        'testing' => 'testing',
        'testing2' => 'testing2',
    ]);

    expect($user->getAttributes())
        ->toBeArray()
        ->toContain('testing')
        ->toContain('testing2');
});

it('supports getting the identifier', function (): void {
    $user = new User(['sub' => 'testing']);

    expect($user->getAuthIdentifier())
        ->toBe('testing');
});

it('supports getting the identifier name', function (): void {
    $user = new User(['sub' => 'testing']);

    expect($user->getAuthIdentifierName())
        ->toBe('id');
});

it('supports getting the password', function (): void {
    $user = new User();

    expect($user->getAuthPassword())
        ->toBe('');
});

it('supports getting the remember token', function (): void {
    $user = new User();

    expect($user->getRememberToken())
        ->toBe('');
});

it('supports getting the remember token name', function (): void {
    $user = new User();

    expect($user->getRememberTokenName())
        ->toBe('');
});

it('supports setting the remember token', function (): void {
    $user = new User();

    expect($user->setRememberToken('testing'))
        ->toBeNull();
});

it('supports JSON serialization', function (): void {
    $user = new User(['testing' => 'testing']);

    expect($user->jsonSerialize())
        ->toBeArray()
        ->toContain('testing');

    expect(json_encode($user))
        ->toBeJson('{"testing":"testing"}');
});
