<?php

namespace UserBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class OAuthToken extends AbstractToken
{
    public function __construct($user)
    {
        parent::__construct(['ROLE_USER']);
        $this->setUser($user);
        $this->setAuthenticated(true);
    }

    public function getCredentials()
    {
        return '';
    }
}
