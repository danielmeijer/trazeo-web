<?php

namespace Sopinet\UserNotificationsBundle\Service;

use Sopinet\UserNotificationsBundle\SopinetUserNotificationsBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sopinet\UserNotificationsBundle\Entity\Notification;

class NotificationHelper {
	private $_container;
				
	function __construct(ContainerInterface $container) {
		$this->_container = $container;
	}

	/**
	 * 
	 * @param String $action
	 * @param String $object
	 */
	function addNotification($action, $object, $object_id = null, $user = null) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		if ($user == null) {
			$user = $this->_container->get('security.context')->getToken()->getUser();
		}
		$userextend = $user->getSopinetUserExtend();
		
		if ($userextend == null) {
			$userextend = new \Sopinet\UserBundle\Entity\SopinetUserExtend();
			$userextend->setUser($user);
			$em->persist($userextend);
			$em->flush();
		}

		//$reNotification = $em->getRepository("SopinetUserNotificationsBundle:Notification");
		$notification = new Notification();
		$notification->setAction($action);
		$notification->setObject($object);
		if ($object_id != null) {
			$notification->setObjectId($object_id);
		}
		$notification->setUser($userextend);
		$notification->setEmail(0);
		$notification->setView(0);
		
		$em->persist($notification);
		$em->flush();
	}
}