<?php

declare(strict_types=1);

use Auth0\Laravel\Cache\LaravelCacheItem;

uses()->group('cache', 'cache.laravel', 'cache.laravel.item');

test('getKey() returns an expected value', function (): void {
    $cacheItem = new LaravelCacheItem('testing', 42, true);

    expect($cacheItem->getKey())
        ->toBe('testing');
});

test('get() returns an expected value when hit', function (): void {
    $cacheItem = new LaravelCacheItem('testing', 42, true);

    expect($cacheItem->get())
        ->toBe(42);
});

test('get() returns null when no hit', function (): void {
    $cacheItem = new LaravelCacheItem('testing', 42, false);

    expect($cacheItem->get())
        ->toBeNull();
});

test('getRawValue() returns an expected value', function (): void {
    $cacheItem = new LaravelCacheItem('testing', 42, false);

    expect($cacheItem->getRawValue())
        ->toBe(42);
});

test('isHit() returns an expected value when hit', function (): void {
    $cacheItem = new LaravelCacheItem('testing', 42, true);

    expect($cacheItem->isHit())
        ->toBeTrue();

    $cacheItem = new LaravelCacheItem('testing', 42, false);

    expect($cacheItem->isHit())
        ->toBeFalse();
});

test('set() alters the stored value as expected', function (): void {
    $cacheItem = new LaravelCacheItem('testing', 42, true);

    expect($cacheItem->get())
        ->toBe(42);

    expect($cacheItem->set(43))
        ->toBe($cacheItem)
        ->get()->toBe(43);
});

test('expiresAt() defaults to +1 year and accepts changes to its value', function (): void {
    $cacheItem = new LaravelCacheItem('testing', 42, true);

    expect($cacheItem->getExpiration())
        ->toBeGreaterThan(new DateTime('now +1 year -1 minute'))
        ->toBeLessThan(new DateTime('now +1 year +1 minute'));

    $cacheItem->expiresAt(new DateTime('now +1 day'));

    expect($cacheItem->getExpiration())
        ->toBeGreaterThan(new DateTime('now +1 day -1 minute'))
        ->toBeLessThan(new DateTime('now +1 day +1 minute'));
});

test('expiresAfter() defaults to +1 year and accepts changes to its value', function (): void {
    $cacheItem = new LaravelCacheItem('testing', 42, true);

    expect($cacheItem->getExpiration())
        ->toBeGreaterThan(new DateTime('now +1 year -1 minute'))
        ->toBeLessThan(new DateTime('now +1 year +1 minute'));

    $cacheItem->expiresAfter(300);

    expect($cacheItem->getExpiration())
        ->toBeGreaterThan(new DateTime('now +250 seconds'))
        ->toBeLessThan(new DateTime('now +350 seconds'));
});

test('miss() returns a configured instance', function (): void {
    $cacheItem = new LaravelCacheItem('testing', 42, true);
    $newCacheItem = $cacheItem->miss('testing123');

    expect($cacheItem->getKey())
        ->toBe('testing');

    expect($newCacheItem->getKey())
        ->toBe('testing123');

    expect($newCacheItem->get())
        ->not()->toBe($cacheItem->get());
});
