<?php

namespace Sopinet\Bundle\UserNotificationsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SopinetUserNotificationsBundle:Default:index.html.twig', array('name' => $name));
    }
}
