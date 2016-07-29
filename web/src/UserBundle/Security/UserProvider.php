<?php

namespace UserBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

use UserBundle\Document\User;

class UserProvider implements UserProviderInterface
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function loadUserByOAuthProvider($providerName, $id)
    {
        $field = "providers.$providerName.id";
        $user = $this->db->wshell->users->findOne([$field => $id]);
        if ($user) {
            return $user;
        }
        return null;
    }

    public function loadUserByUsername($username)
    {
        $user = $this->db->wshell->users->findOne(['username' => $username]);
        if ($user) {
            return $user;
        }
        return null;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'UserBundle\Document\User';
    }
}
