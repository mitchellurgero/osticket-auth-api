<?php

namespace ApiAuth;

use AccessDenied;
use StaffAuthenticationBackend;
use StaffSession;

class StaffAuthBackend extends StaffAuthenticationBackend
{
    use Authenticator;

    public static $name = 'External API Authentication';
    public static $id = 'externalapi';
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function supportsInteractiveAuthentication()
    {
        return true;
    }

    public function authenticate($username, $password)
    {
        $apiResponse = $this->getApiResponse($username, $password);
        if ($apiResponse->success) {
            if ($user = StaffSession::lookup($username)) {
                return $user;
            } else {
                return new AccessDenied('Your credentials are valid but you do not have a staff account.');
            }
        } elseif ($apiResponse->error) {
            return new AccessDenied($apiResponse->error);
        } else {
            return new AccessDenied('Unable to validate login.');
        }
    }

    public function renderExternalLink()
    {
        return false;
    }

    public function supportsPasswordChange()
    {
        return false;
    }

    public function supportsPasswordReset()
    {
        return false;
    }
}
