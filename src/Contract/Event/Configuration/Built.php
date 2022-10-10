<?php

declare(strict_types=1);

namespace Auth0\Laravel\Contract\Event\Configuration;

use Auth0\SDK\Configuration\SdkConfiguration;

interface Built
{
    /**
     * AuthenticationFailed constructor.
     *
     * @param  SdkConfiguration  $configuration  an instance of Auth0\SDK\Configuration\SdkConfiguration for use with the Auth0-PHP SDK
     */
    public function __construct(SdkConfiguration $configuration);

    /**
     * Returns the exception to be thrown.
     */
    public function getConfiguration(): SdkConfiguration;

    /**
     * Determine whether the provided exception will be thrown by the SDK.
     *
     * @param  SdkConfiguration  $configuration  an instance of Auth0\SDK\Configuration\SdkConfiguration for use with the Auth0-PHP SDK
     */
    public function setConfiguration(SdkConfiguration $configuration): self;
}
