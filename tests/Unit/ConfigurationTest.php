<?php

declare(strict_types=1);

use Auth0\Laravel\Configuration;

uses()->group('Configuration');

test('stringToArrayOrNull() behaves as expected', function (): void {
    expect(Configuration::stringToArrayOrNull('foo bar baz'))
        ->toBe(['foo', 'bar', 'baz']);
});

test('stringToArray() behaves as expected', function (): void {
    expect(Configuration::stringToArray('foo bar baz'))
        ->toBe(['foo', 'bar', 'baz']);
});

test('stringToBoolOrNull() behaves as expected', function (): void {
    expect(Configuration::stringToBoolOrNull('true'))
        ->toBeTrue();

    expect(Configuration::stringToBoolOrNull('false'))
        ->toBeFalse();

    expect(Configuration::stringToBoolOrNull('foo'))
        ->toBeNull();

    expect(Configuration::stringToBoolOrNull('foo', true))
        ->toBeTrue();

    expect(Configuration::stringToBoolOrNull('foo', false))
        ->toBeFalse();
});
