<?php

declare(strict_types=1);

use Auth0\Laravel\Bridges\SessionBridge;
use Auth0\SDK\Contract\StoreInterface;

uses()->group('session-store');

it('throws an exception when an empty prefix is provided', function (): void {
    expect(function () {
        new SessionBridge(
            prefix: '',
        );
    })->toThrow(InvalidArgumentException::class);
});

it('accepts and uses a specified prefix', function (): void {
    $prefix = uniqid();

    $store = new SessionBridge(
        prefix: $prefix,
    );

    expect($store)
        ->toBeInstanceOf(StoreInterface::class)
        ->getPrefix()->toBe($prefix);
});

it('allows updating the prefix', function (): void {
    $store = new SessionBridge();

    expect($store)
        ->toBeInstanceOf(StoreInterface::class)
        ->getPrefix()->toBe('auth0');

    $prefix = uniqid();
    $store->setPrefix($prefix);

    expect($store)
        ->toBeInstanceOf(StoreInterface::class)
        ->getPrefix()->toBe($prefix);
});

it('supports CRUD operations', function (): void {
    $prefix = uniqid();

    $store = new SessionBridge(
        prefix: $prefix,
    );

    expect($store)
        ->toBeInstanceOf(StoreInterface::class)
        ->get('test')->toBeNull()
        ->set('test', 'value')->toBeNull()
        ->get('test')->toBe('value')
        ->getAll()->toBe([$prefix . '_test' => 'value'])
        ->set('test2', 'value2')->toBeNull()
        ->getAll()->toBe([$prefix . '_test' => 'value', $prefix . '_test2' => 'value2'])
        ->delete('test')->toBeNull()
        ->getAll()->toBe([$prefix . '_test2' => 'value2'])
        ->set('test3', 'value3')->toBeNull()
        ->getAll()->toBe([$prefix . '_test2' => 'value2', $prefix . '_test3' => 'value3'])
        ->purge()->toBeNull()
        ->getAll()->toBe([]);
});
