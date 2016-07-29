<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $username = $this->get('security.token_storage')->getToken()->getUser();
        $user = $this->get('mongo')->citates->user->findOne(['username' => $username]);
        $citateCount = $this->get('mongo')->citates->citate->count();
        if (!$user) {
            $skip = rand(0, $citateCount-1);
            $citate = $this->get('mongo')->citates->citate->findOne([], ['skip'=>$skip])->getArrayCopy();
            return $this->render('AppBundle:Default:index.html.twig', [
              'citate' => $citate
            ]);
        }
        $user = $user->getArrayCopy();


        $votes = $user['votes']->getArrayCopy();

        $userVotesCount = count($votes);
        if ($citateCount == 0 || $userVotesCount/$citateCount == 1) {
            return $this->render('AppBundle:Default:index.html.twig', [
              'citate' => false
            ]);
        }

        // user vote > 80% citates
        if ($userVotesCount/$citateCount > 0.8) {
            $_ids = [];
            foreach($votes as $voteId){
                $_ids[] = new \MongoDB\BSON\ObjectID($voteId);
            }
            $notVotingCursor = $this->get('mongo')->citates->citate->find(['_id' => ['$nin' => $_ids]])->toArray();
            $skip = rand(0, count($notVotingCursor)-1);
            $citate = $notVotingCursor[$skip]->getArrayCopy();
        } else {
            $finded = false;
            while (!$finded) {
                $skip = rand(0, $citateCount-1);
                $citate = $this->get('mongo')->citates->citate->findOne([], ['skip'=>$skip])->getArrayCopy();
                $curId = strval($citate['_id']);
                if (!in_array($curId, $votes)) {
                    $finded = true;
                }
            }
        }
        $citate['_id'] = strval($citate['_id']);

        return $this->render('AppBundle:Default:index.html.twig', [
          'citate' => $citate
        ]);
    }

    /**
     * @Route("/rating", name="rating")
     */
    public function ratingAction(Request $request)
    {
        $citates = $this->get('mongo')->citates->citate->find([], ['sort'=> ['score'=>-1], 'limit'=> 30 ])->toArray();

        return $this->render('AppBundle:Default:rating.html.twig', [
          'citates' => $citates
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request)
    {
        $params = $request->query->all();
        // check app_id+uid+secret_key
        $hash = md5(5567436 . $params['uid'] . '0fSxNuGth2iiTiuKWfOU');
        if ($params['hash'] == $hash) {

            $user = $this->get('mongo')->citates->user->findOne(['uid' => $params['uid']]);
            $username = trim($params['first_name'] . ' ' . $params['last_name']);

            if (!$user) {
                // register new user
                $user = [
                    'uid' => $params['uid'],
                    'photo' => $params['photo'],
                    'username'  => $username,
                ];
                $this->get('mongo')->citates->user->insertOne($user);
            }
            $token = new UsernamePasswordToken(
                $username,
                $params['hash'],
                'main',
                ["ROLE_USER"]
            );
            $this->get('security.token_storage')->setToken($token);
        }

        return new RedirectResponse('/');
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profileAction(Request $request)
    {
        $username = $this->get('security.token_storage')->getToken()->getUser();
        $user = $this->get('mongo')->citates->user->findOne(['username' => $username])->getArrayCopy();

        $citates = $this->get('mongo')->citates->citate->find(['uid' => $user['uid']])->toArray();

        return $this->render('AppBundle:Default:profile.html.twig', [
          'userpic' => $user['photo'],
          'citates' => $citates
        ]);
    }

    // AJAX ACTIONS

    /**
     * @Route("/vote", name="vote")
     */
    public function voteAction(Request $request)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            return new JsonResponse(['result' => 'omg. login, please']);
        }
        $args = $request->query->all();

        $username = $this->get('security.token_storage')->getToken()->getUser();
        $user = $this->get('mongo')->citates->user->findOne(['username' => $username]);
        $user->votes[] = $args['id'];
        $result = $this->get('mongo')->citates->user->replaceOne([ '_id' => $user->_id ], $user);

        $citate = $this->get('mongo')->citates->citate->findOne(['_id' => new \MongoDB\BSON\ObjectID($args['id'])]);
        $citate->score += $args['vote'] === '+' ? 1:-1;
        $this->get('mongo')->citates->citate->replaceOne([ '_id' => $citate->_id ], $citate);

        return new JsonResponse(['result' => $citate->score]);
    }

    /**
     * @Route("/add", name="add")
     */
    public function addAction(Request $request)
    {
        if(!$this->get('security.authorization_checker')->isGranted('ROLE_USER')){
            return new RedirectResponse('/');
        }

        $username = $this->get('security.token_storage')->getToken()->getUser();
        $user = $this->get('mongo')->citates->user->findOne(['username' => $username])->getArrayCopy();

        $citateText = $request->request->get('citate');
        $citate = [
            'uid' => $user['uid'],
            'text' => $citateText,
            'score' => 0
        ];

        $finded = $this->get('mongo')->citates->citate->findOne(['text' => $citateText]);
        if (!$finded) {
            $citates = $this->get('mongo')->citates->citate->insertOne($citate);
        }
        return new RedirectResponse('/');
    }
}
