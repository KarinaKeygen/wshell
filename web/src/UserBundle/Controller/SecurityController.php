<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use ohmy\Auth2;
use UserBundle\Document\User;
use UserBundle\Security\OAuthToken;


class SecurityController extends Controller
{
    public function loginAction(Request $request)
    {
        $helper = $this->get('security.authentication_utils');
        $error = $helper->getLastAuthenticationError();
        $lastUsername = $helper->getLastUsername();

        return $this->render('UserBundle:Security:login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    public function oauthAction(Request $request)
    {
        if ($request->request->has('vk')) {
            return $this->redirectToRoute('vk_login');
        }
        if ($request->request->has('github')) {
            return $this->redirectToRoute('github_login');
        }

        $e = new AuthenticationException('Неизвестный OAuth провайдер');
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $e);
        return $this->redirectToRoute('login');
    }


    public function vkLoginAction()
    {
        $params = $this->getParameter('oauth.vk');

        $vk = Auth2::legs(3)
            # configuration
            ->set($params)
            # oauth flow
            ->authorize('https://oauth.vk.com/authorize')
            ->access('https://oauth.vk.com/access_token')
            ->finally(function ($data) use (&$access_token) {
                $access_token = $data['access_token'];
            });

        # access vk api
        $vk->GET("https://api.vk.com/method/getProfiles?access_token=$access_token", null,
            ['User-Agent' => 'wshell-auth'])
            ->then(function ($response) use (&$access_token) {

                $userVk = current($response->json()['response']);
                $user = $this->get('my_user_provider')->loadUserByOAuthProvider( 'vk', $userVk['uid'] );
                if (!$user) {
                    // register new user
                    $username = trim($userVk['first_name'] . ' ' . $userVk['last_name']);
                    $providers = [
                        'vk' => [
                            'id'    => $userVk['uid'],
                            'name' => $username
                        ]
                    ];
                    $user = new User([
                        'providers' => $providers,
                        'roles'     => ['ROLE_USER'],
                        'username'  => $username,
                    ]);

                    $this->get('mongo')->wshell->users->insertOne($user);
                }

                $token = new OAuthToken($user);
                $this->get('security.token_storage')->setToken($token);
            });

            return $this->redirectToRoute('news');
    }

    public function githubLoginAction()
    {
        $params = $this->getParameter('oauth.github');

        $github = Auth2::legs(3)
            # configuration
            ->set($params)
            # oauth flow
            ->authorize('https://github.com/login/oauth/authorize')
            ->access('https://github.com/login/oauth/access_token')
            ->finally(function ($data) use (&$access_token) {
                $access_token = $data['access_token'];
            });

        # access github api
        $github->GET("https://api.github.com/user?access_token=$access_token", null,
            ['User-Agent' => 'wshell-auth'])
            ->then(function ($response) use ($access_token) {

                $userGithub = $response->json();
                $user = $this->get('my_user_provider')->loadUserByOAuthProvider( 'github', $userGithub['id'] );
                if (!$user) {
                    // register new user
                    $providers = [
                        'github' => [
                            'id'    =>  $userGithub['id'],
                            'login' => $userGithub['login'],
                            'email' => $userGithub['email'],
                            'avatar_url' => $userGithub['avatar_url'],
                            'html_url' => $userGithub['html_url'],
                            'name' => $userGithub['name'],
                            'location' => $userGithub['location'],
                            'public_repos' => $userGithub['public_repos'],
                        ]
                    ];
                    $user = new User([
                        'providers' => $providers,
                        'roles'     => ['ROLE_USER'],
                        'username'  => $userGithub['login'],
                    ]);

                    $this->get('mongo')->wshell->users->insertOne($user);
                }

                $token = new OAuthToken($user);
                $this->get('security.token_storage')->setToken($token);
            });

            return $this->redirectToRoute('news');
    }
}
