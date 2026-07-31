<?php

declare(strict_types=1);

namespace Auth0\Laravel\Entities;

use Auth0\SDK\API\Management\Wrapper\ManagementClient;
use Auth0\SDK\Configuration\SdkConfiguration;
use Auth0\SDK\Contract\Auth0Interface;

/**
 * @api
 */
interface InstanceEntityContract extends EntityContract
{
    /**
     * Create/return instance of the Auth0-PHP SdkConfiguration.
     */
    public function getConfiguration(): SdkConfiguration;

    /**
     * Create/return instance of the Auth0-PHP SDK.
     */
    public function getSdk(): Auth0Interface;

    /**
     * Returns a v9 Management API client (base Auth0-PHP ManagementClient).
     *
     * @param array<string, mixed> $options Overrides passed to ManagementClientOptions.
     */
    public function management(array $options = []): ManagementClient;

    /**
     * Resets and cleans up the internal state of the SDK.
     */
    public function reset(): self;

    /**
     * Assign the Auth0-PHP SdkConfiguration.
     *
     * @param null|array<string>|SdkConfiguration $configuration
     */
    public function setConfiguration(SdkConfiguration | array | null $configuration): self;

    /**
     * Create/return instance of the Auth0-PHP SDK.
     *
     * @param Auth0Interface $sdk
     */
    public function setSdk(Auth0Interface $sdk): Auth0Interface;
}
