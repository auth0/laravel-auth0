<?php

namespace Auth0\Login\Sdk;

use Auth0\SDK\Auth0 as Auth0Sdk;
use Illuminate\Http\RedirectResponse;

class Auth0 extends Auth0Sdk
{
    /**
     * @inheritDoc
     *
     * Extends the curernt behaviour for now to let the framework return a redirect response. Allowing Laravel
     * to handle the sessions and cookies nicely.
     *
     */
    public function login($state = null, $connection = null, array $additionalParams = [])
    {
        $params = [];
        if ($this->audience) {
            $params['audience'] = $this->audience;
        }

        if ($this->scope) {
            $params['scope'] = $this->scope;
        }

        if ($state === null) {
            $state = $this->stateHandler->issue();
        } else {
            $this->stateHandler->store($state);
        }

        $params['response_mode'] = $this->responseMode;

        if (!empty($additionalParams) && is_array($additionalParams)) {
            $params = array_replace($params, $additionalParams);
        }

        $url = $this->authentication->get_authorize_link(
            $this->responseType,
            $this->redirectUri,
            $connection,
            $state,
            $params
        );

        return new RedirectResponse($url);
    }
}
