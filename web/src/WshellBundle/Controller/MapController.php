<?php

namespace WshellBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class MapController extends Controller
{
    public function mapAction()
    {
        return $this->render('WshellBundle:Map:map.html.twig', [
        ]);
    }

    public function observerAction()
    {
        return $this->render('WshellBundle:Map:observer.html.twig', [
        ]);
    }
}
