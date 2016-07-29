<?php

namespace UserBundle\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;

use UserBundle\Document\User;
use UserBundle\Validator\Check;

class Authentificator implements SimpleFormAuthenticatorInterface
{
    private $encoder;
    private $db;

    public function __construct(UserPasswordEncoderInterface $encoder, $db)
    {
        $this->encoder = $encoder;
        $this->db = $db;
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        $user = $userProvider->loadUserByUsername($token->getUsername());
        if(!$user) {
            // try register user
            if ($token->getUsername() && $token->getCredentials()) {
                if( !preg_match('#^[a-zA-Z0-9_-]{4,16}$#', $token->getUsername()) ) {
                    throw new CustomUserMessageAuthenticationException('3<len(Nickname)<17 and contains only a-zA-Z0-9_-');
                }

                $user = new User([
                    'roles'     => ['ROLE_USER'],
                    'username'  => $token->getUsername(),
                ]);
                $password = $this->encoder->encodePassword($user, $token->getCredentials());
                $user->password = $password;

                $this->db->wshell->users->insertOne($user);

            } else {
                throw new CustomUserMessageAuthenticationException('Empty password');
            }
        }

        $passwordValid = $this->encoder->isPasswordValid($user, $token->getCredentials());

        if ($passwordValid) {
            return new UsernamePasswordToken(
                $user,
                $user->getPassword(),
                $providerKey,
                $user->getRoles()
            );
        }

        throw new CustomUserMessageAuthenticationException('Invalid password');
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken
            && $token->getProviderKey() === $providerKey;
    }

    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }
}
