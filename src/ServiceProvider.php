<?php

declare(strict_types=1);

namespace Auth0\Laravel;

final class ServiceProvider extends \Spatie\LaravelPackageTools\PackageServiceProvider implements \Auth0\Laravel\Contract\ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function configurePackage(
        \Spatie\LaravelPackageTools\Package $package
    ): void {
        $package
            ->name('auth0')
            ->hasConfigFile();
    }

    /**
     * @inheritdoc
     */
    public function registeringPackage(): void
    {
        app()->singleton(Auth0::class, static function (): \Auth0\Laravel\Auth0 {
            return new Auth0();
        });

        app()->singleton('auth0', static function (): \Auth0\Laravel\Auth0 {
            return app()->make(Auth0::class);
        });

        app()->singleton(StateInstance::class, static function (): \Auth0\Laravel\StateInstance {
            return new StateInstance();
        });

        app()->singleton(\Auth0\Laravel\Auth\User\Repository::class, static function (): \Auth0\Laravel\Auth\User\Repository {
            return new \Auth0\Laravel\Auth\User\Repository();
        });
    }

    /**
     * @inheritdoc
     */
    public function bootingPackage(): void
    {
        auth()->provider('auth0', static function ($app, array $config): \Auth0\Laravel\Auth\User\Provider {
            return new \Auth0\Laravel\Auth\User\Provider(app()->make($config['repository']));
        });

        auth()->extend('auth0', static function ($app, $name, array $config): \Auth0\Laravel\Auth\Guard {
            return new \Auth0\Laravel\Auth\Guard(auth()->createUserProvider($config['provider']), $app->make('request'));
        });
    }
}
