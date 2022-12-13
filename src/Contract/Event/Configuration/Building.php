<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Configuration;

interface Building
{
    /**
     * AuthenticationFailed constructor.
     *
     * @param  array  $configuration  a configuration array for use with the Auth0-PHP SDK
     */
    public function __construct(array $configuration);

    /**
     * Returns the exception to be thrown.
     */
    public function getConfiguration(): array;

    /**
     * Determine whether the provided exception will be thrown by the SDK.
     *
     * @param  array  $configuration  an configuration array for use with the Auth0-PHP SDK
     */
    public function setConfiguration(array $configuration): self;
}
