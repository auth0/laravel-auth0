<?php

declare(strict_types=1);

use Auth0\Laravel\Bridges\CacheBridge;
use Auth0\Laravel\Bridges\CacheItemBridge;
use Psr\Cache\CacheItemInterface;

uses()->group('cache', 'cache.laravel', 'cache.laravel.pool');

test('getItem(), hasItem() and save() behave as expected', function (): void {
    $pool = new CacheBridge();
    $cache = $pool->getItem('testing');

    expect($pool)
        ->hasItem('testing')->toBeFalse();

    expect($cache)
        ->toBeInstanceOf(CacheItemBridge::class)
        ->get()->toBeNull()
        ->isHit()->toBeFalse();

    $cache->set(42);

    expect($cache)
        ->get()->toBeNull();

    expect($pool)
        ->save($cache)->toBeTrue();

    expect($pool)
        ->hasItem('testing')->toBeTrue();

    $cache = $pool->getItem('testing');

    expect($cache)
        ->toBeInstanceOf(CacheItemBridge::class)
        ->isHit()->toBeTrue()
        ->get()->toBeNull()
        ->set(42)
        ->get()->toBe(42);

    $results = $pool->getItems();

    expect($results)
        ->toBeArray()
        ->toHaveCount(0);

    $results = $pool->getItems(['testing' => uniqid()]);

    expect($results['testing'])
        ->toBeInstanceOf(CacheItemBridge::class)
        ->isHit()->toBeTrue()
        ->get()->not()->toBe(42);

    $this->app[\Illuminate\Cache\CacheManager::class]
        ->getStore()
        ->put('testing', false, 60);

    $cache = $pool->getItem('testing');

    expect($pool)
        ->hasItem('testing')->toBeFalse();

    expect($cache)
        ->toBeInstanceOf(CacheItemBridge::class)
        ->get()->toBeNull()
        ->isHit()->toBeFalse();

    $cacheMock = Mockery::mock(CacheItemInterface::class);

    expect($pool)
        ->save($cacheMock)->toBeFalse();
});

test('save() with a negative expiration value is deleted', function (): void {
    $pool = new CacheBridge();
    $cache = new CacheItemBridge('testing', 42, true, new DateTime('now - 1 year'));

    expect($pool)->hasItem('testing')->toBeFalse();

    $pool->save($cache);

    expect($pool)->hasItem('testing')->toBeFalse();
});

test('saveDeferred() behaves as expected', function (): void {
    $pool = new CacheBridge();
    $cache = new CacheItemBridge('testing', 42, true, new DateTime('now + 1 hour'));

    expect($pool)
        ->hasItem('testing')->toBeFalse()
        ->saveDeferred($cache)->toBeTrue()
        ->hasItem('testing')->toBeFalse()
        ->commit()->toBeTrue()
        ->hasItem('testing')->toBeTrue();
});

test('save() with a false value is discarded', function (): void {
    $pool = new CacheBridge();
    $cache = new CacheItemBridge('testing', false, true, new DateTime('now + 1 hour'));

    expect($pool)
        ->hasItem('testing')->toBeFalse()
        ->save($cache)->toBeTrue()
        ->hasItem('testing')->toBeFalse();
});

test('saveDeferred() returns false when the wrong type of interface is saved', function (): void {
    $pool = new CacheBridge();
    $cache = new CacheItemBridge('testing', 42, true, new DateTime('now + 1 hour'));

    $cache = new class implements CacheItemInterface {
        public function getKey(): string
        {
            return 'testing';
        }

        public function get(): mixed
        {
            return 42;
        }

        public function isHit(): bool
        {
            return true;
        }

        public function set(mixed $value): static
        {
            return $this;
        }

        public function expiresAt($expiration): static
        {
            return $this;
        }

        public function expiresAfter($time): static
        {
            return $this;
        }
    };

    expect($pool)
        ->hasItem('testing')->toBeFalse()
        ->saveDeferred($cache)->toBeFalse()
        ->hasItem('testing')->toBeFalse();
});

test('deleteItem() behaves as expected', function (): void {
    $pool = new CacheBridge();
    $cache = new CacheItemBridge('testing', 42, true, new DateTime('now + 1 minute'));

    expect($pool)->hasItem('testing')->toBeFalse();

    $pool->save($cache);

    expect($pool)->hasItem('testing')->toBeTrue();

    $cache = $pool->getItem('testing');

    expect($cache)
        ->isHit()->toBeTrue()
        ->get()->toBe(42);

    $pool->deleteItem('testing');

    expect($pool)
        ->hasItem('testing')->toBeFalse();

    $cache = $pool->getItem('testing');

    expect($cache)
        ->isHit()->toBeFalse()
        ->get()->toBeNull();
});

test('deleteItems() behaves as expected', function (): void {
    $pool = new CacheBridge();

    expect($pool)
        ->hasItem('testing1')->toBeFalse()
        ->hasItem('testing2')->toBeFalse()
        ->hasItem('testing3')->toBeFalse();

    $cache = new CacheItemBridge('testing1', uniqid(), true, new DateTime('now + 1 minute'));
    $pool->save($cache);

    $cache = new CacheItemBridge('testing2', uniqid(), true, new DateTime('now + 1 minute'));
    $pool->save($cache);

    $cache = new CacheItemBridge('testing3', uniqid(), true, new DateTime('now + 1 minute'));
    $pool->save($cache);

    expect($pool)
        ->hasItem('testing1')->toBeTrue()
        ->hasItem('testing2')->toBeTrue()
        ->hasItem('testing3')->toBeTrue();

    $results = $pool->getItems(['testing1' => 1, 'testing2' => 2, 'testing3' => 3]);

    expect($results)
        ->toHaveKey('testing1')
        ->toHaveKey('testing2')
        ->toHaveKey('testing3')
        ->testing1->isHit()->toBeTrue()
        ->testing2->isHit()->toBeTrue()
        ->testing3->isHit()->toBeTrue();

    expect($pool)
        ->deleteItems(['testing1', 'testing2', 'testing3'])->toBeTrue()
        ->hasItem('testing1')->toBeFalse()
        ->hasItem('testing2')->toBeFalse()
        ->hasItem('testing3')->toBeFalse()
        ->deleteItems(['testing4', 'testing5', 'testing6'])->toBeFalse();
});

test('clear() behaves as expected', function (): void {
    $pool = new CacheBridge();

    expect($pool)
        ->hasItem('testing1')->toBeFalse()
        ->hasItem('testing2')->toBeFalse()
        ->hasItem('testing3')->toBeFalse();

    $cache = new CacheItemBridge('testing1', uniqid(), true, new DateTime('now + 1 minute'));
    $pool->save($cache);

    $cache = new CacheItemBridge('testing2', uniqid(), true, new DateTime('now + 1 minute'));
    $pool->save($cache);

    $cache = new CacheItemBridge('testing3', uniqid(), true, new DateTime('now + 1 minute'));
    $pool->save($cache);

    expect($pool)
        ->hasItem('testing1')->toBeTrue()
        ->hasItem('testing2')->toBeTrue()
        ->hasItem('testing3')->toBeTrue();

    $results = $pool->getItems(['testing1' => 1, 'testing2' => 2, 'testing3' => 3]);

    expect($results)
        ->toHaveKey('testing1')
        ->toHaveKey('testing2')
        ->toHaveKey('testing3')
        ->testing1->isHit()->toBeTrue()
        ->testing2->isHit()->toBeTrue()
        ->testing3->isHit()->toBeTrue();

    $pool->clear();

    expect($pool)
        ->hasItem('testing1')->toBeFalse()
        ->hasItem('testing2')->toBeFalse()
        ->hasItem('testing3')->toBeFalse();
});
