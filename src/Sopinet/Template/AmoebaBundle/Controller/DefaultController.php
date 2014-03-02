<?php

namespace Sopinet\Template\AmoebaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SopinetTemplateAmoebaBundle:Default:index.html.twig', array('name' => $name));
    }
}
