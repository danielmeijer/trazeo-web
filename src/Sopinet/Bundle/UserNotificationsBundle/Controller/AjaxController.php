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

	/**
	 * @Route("/{not_id}/view", name="sopinetusernotifications_view")
	 * @Template
	 */
    public function viewAction($not_id)
    {
		$em = $this->getDoctrine()->getManager();
		$not = $this->container->get('sopinet_user_notification');
		$not->clearNotification(null,$not_id);
    	//die("HOLA");
    }
}