<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract;

interface ServiceProvider
{
    /**
     * Configure package details for Laravel's consumption.
     */
    public function configurePackage(\Spatie\LaravelPackageTools\Package $package): void;

    /**
     * Register application services.
     */
    public function registeringPackage(): void;

    /**
     * Register middleware and guard.
     */
    public function bootingPackage(): void;
}
