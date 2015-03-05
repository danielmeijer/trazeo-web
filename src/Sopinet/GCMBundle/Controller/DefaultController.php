<?php

namespace Sopinet\GCMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SopinetGCMBundle:Default:index.html.twig', array('name' => $name));
    }
}
