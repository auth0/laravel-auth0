<?php

namespace Auth0\Login\Tests\Integration;

use Auth0\Login\Auth0Service;
use Auth0\SDK\API\Helpers\State\SessionStateHandler;
use Auth0\SDK\API\Helpers\State\StateHandler;
use Auth0\SDK\Store\StoreInterface;
use Illuminate\Http\RedirectResponse;

class Auth0ServiceTest extends Testcase
{
    /**
     * @var Auth0Service
     */
    protected $auth0Service;

    public function setUp(): void
    {
        parent::setUp();
        $this->auth0Service = $this->app->get(Auth0Service::class);
    }

    public function testInitializeLoginWillReturnRedirectResponse()
    {
        $this->assertInstanceOf(RedirectResponse::class, $this->auth0Service->login());
    }

    public function testInitializeLoginWillIssueNewState()
    {
        /** @var StoreInterface $storage */
        $storage = $this->app->get(StoreInterface::class);
        $this->assertEmpty($storage->get(SessionStateHandler::STATE_NAME));

        $this->auth0Service->login();

        $this->assertNotEmpty($storage->get(SessionStateHandler::STATE_NAME));
    }

    public function testInitializeLoginWillOverwriteOldState()
    {
        /** @var StoreInterface $storage */
        $storage = $this->app->get(StoreInterface::class);
        $handler = $this->app->get(StateHandler::class);
        $handler->issue();
        $oldState = $storage->get(SessionStateHandler::STATE_NAME);

        $this->auth0Service->login();

        $this->assertNotEmpty($oldState);
        $this->assertNotEmpty($storage->get(SessionStateHandler::STATE_NAME));
        $this->assertNotEquals($oldState, $storage->get(SessionStateHandler::STATE_NAME));
    }
}
