<?php

declare(strict_types=1);

use Auth0\Laravel\Configuration;

uses()->group('Configuration');

test('stringToArrayOrNull() behaves as expected', function (): void {
    expect(Configuration::stringToArrayOrNull('foo bar baz'))
        ->toBe(['foo', 'bar', 'baz']);

    expect(Configuration::stringToArrayOrNull(['foo', 'bar', 'baz']))
        ->toBe(['foo', 'bar', 'baz']);

    expect(Configuration::stringToArrayOrNull('   '))
        ->toBeNull();
});

test('stringToArray() behaves as expected', function (): void {
    expect(Configuration::stringToArray('foo bar baz'))
        ->toBe(['foo', 'bar', 'baz']);

    expect(Configuration::stringToArray(['foo', 'bar', 'baz']))
        ->toBe(['foo', 'bar', 'baz']);

    expect(Configuration::stringToArray('   '))
        ->toBeArray()
        ->toHaveCount(0);
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

test('stringOrNull() behaves as expected', function (): void {
    expect(Configuration::stringOrNull(123))
        ->toBeNull();

    expect(Configuration::stringOrNull('     456 '))
        ->toEqual('456');

    expect(Configuration::stringOrNull('      '))
        ->toBeNull();

    expect(Configuration::stringOrNull('empty'))
        ->toBeNull();

    expect(Configuration::stringOrNull('(empty)'))
        ->toBeNull();

    expect(Configuration::stringOrNull('null'))
        ->toBeNull();

    expect(Configuration::stringOrNull('(null)'))
        ->toBeNull();
});

test('stringOrIntToIntOrNull() behaves as expected', function (): void {
    expect(Configuration::stringOrIntToIntOrNull(123))
        ->toEqual(123);

    expect(Configuration::stringOrIntToIntOrNull('     456 '))
        ->toEqual(456);

    expect(Configuration::stringOrIntToIntOrNull('      '))
        ->toBeNull();

    expect(Configuration::stringOrIntToIntOrNull('     abc '))
        ->toBeNull();
});

test('get() ignores quickstart placeholders', function (): void {
    putenv('AUTH0_DOMAIN={DOMAIN}');
    putenv('AUTH0_CLIENT_ID={CLIENT_ID}');
    putenv('AUTH0_CLIENT_SECRET={CLIENT_SECRET}');
    putenv('AUTH0_AUDIENCE={API_IDENTIFIER}');
    putenv('AUTH0_CUSTOM_DOMAIN=https://example.com');

    expect(Configuration::get(Configuration::CONFIG_CUSTOM_DOMAIN))
        ->toBeString('https://example.com');

    expect(Configuration::get(Configuration::CONFIG_DOMAIN))
        ->toBeNull();

    expect(Configuration::get(Configuration::CONFIG_CLIENT_ID))
        ->toBeNull();

    expect(Configuration::get(Configuration::CONFIG_CLIENT_SECRET))
        ->toBeNull();

    expect(Configuration::get(Configuration::CONFIG_AUDIENCE))
        ->toBeNull();
});

test('get() behaves as expected', function (): void {
    config(['test' => [
        Configuration::CONFIG_AUDIENCE => implode(',', [uniqid(), uniqid()]),
        Configuration::CONFIG_SCOPE => [],
        Configuration::CONFIG_ORGANIZATION => '',

        Configuration::CONFIG_USE_PKCE => true,
        Configuration::CONFIG_HTTP_TELEMETRY => 'true',
        Configuration::CONFIG_COOKIE_SECURE => 123,
        Configuration::CONFIG_PUSHED_AUTHORIZATION_REQUEST => false,

        'tokenLeeway' => 123,
    ]]);

    define('AUTH0_OVERRIDE_CONFIGURATION', 'test');

    expect(Configuration::get(Configuration::CONFIG_AUDIENCE))
        ->toBeArray()
        ->toHaveCount(2);

    expect(Configuration::get(Configuration::CONFIG_SCOPE))
        ->toBeNull();

    expect(Configuration::get(Configuration::CONFIG_ORGANIZATION))
        ->toBeNull();

    expect(Configuration::get(Configuration::CONFIG_USE_PKCE))
        ->toBeTrue();

    expect(Configuration::get(Configuration::CONFIG_HTTP_TELEMETRY))
        ->toBeTrue();

    expect(Configuration::get(Configuration::CONFIG_COOKIE_SECURE))
        ->toBeNull();

    expect(Configuration::get(Configuration::CONFIG_PUSHED_AUTHORIZATION_REQUEST))
        ->toBeFalse();

    expect(Configuration::get('tokenLeeway'))
        ->toBeInt()
        ->toEqual(123);
});
