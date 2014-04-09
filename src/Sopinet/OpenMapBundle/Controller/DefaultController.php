<?php

namespace Sopinet\OpenMapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SopinetOpenMapBundle:Default:index.html.twig', array('name' => $name));
    }
}
