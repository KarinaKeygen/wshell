<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    public function indexAction()
    {
        return $this->render('AdminBundle:Default:index.html.twig');
    }
}
