<?php

namespace Sopinet\Bundle\GamificationBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SopinetGamificationBundle:Default:index.html.twig', array('name' => $name));
    }
}
