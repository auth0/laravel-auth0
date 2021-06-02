<?php

namespace Auth0\Login;

use Auth0\SDK\Auth0 as BaseAuth0;

class Auth0 extends BaseAuth0
{
    protected function getAuthorizationCode()
    {
        $code    = request()->get('code');
        $hasCode = request()->has('code');

        if ($this->responseMode === 'query' && $hasCode) {
            $code = request()->get('code');
        } elseif ($this->responseMode === 'form_post' && $hasCode) {
            $code = request()->post('code');
        }

        return $code;
    }

    public function getState()
    {
        $state = null;
        $hasStateKey = request()->has(self::TRANSIENT_STATE_KEY);

        if ($this->responseMode === 'query' && $hasStateKey) {
            $state = request()->get(self::TRANSIENT_STATE_KEY);
        } else if ($this->responseMode === 'form_post' && $hasStateKey) {
            $state = request()->post(self::TRANSIENT_STATE_KEY);
        }

        return $state;
    }

    public function getInvitationParameters()
    {
        $invite  = null;
        $orgId   = null;
        $orgName = null;

        $invitation = request()->get('invitation');
        $organization = request()->get('organization');
        $organizationName = request()->get('organization_name');

        if ($this->responseMode === 'query') {
            $invite  = ($invitation ? filter_var($invitation, FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE) : null);
            $orgId   = ($organization ? filter_var($organization, FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE) : null);
            $orgName = ($organizationName ? filter_var($organizationName, FILTER_SANITIZE_STRING, FILTER_NULL_ON_FAILURE) : null);
        }

        if ($invite && $orgId && $orgName) {
            return (object) [
                'invitation' => $invite,
                'organization' => $orgId,
                'organizationName' => $orgName
            ];
        }

        return null;
    }
}