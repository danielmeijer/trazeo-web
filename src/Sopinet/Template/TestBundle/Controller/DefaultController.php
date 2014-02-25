<?php

namespace Sopinet\Template\TestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SopinetTemplateTestBundle:Default:index.html.twig', array('name' => $name));
    }
}
