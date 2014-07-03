<?php

namespace Sopinet\Bundle\UserNotificationsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/sopinetusernotifications")
 */
class AjaxController extends Controller
{
	/**
	 * @Route("/clear", name="sopinetusernotifications_clear")
	 * @Template
	 */
    public function clearAction()
    {
		$not = $this->container->get('sopinet_user_notification');
		return array('count' => $not->clearNotifications());
    	//die("HOLA");
    }
}