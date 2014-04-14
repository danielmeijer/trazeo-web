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
	 * Add notification for user logged (or user by parameter)
	 * 
	 * @param String $action
	 * @param String $object
	 * @param Integer $object_id (optional)
	 * @param User $user (optional)
	 */
	function addNotification($action, $objects = null, $objects_id = null, $user = null) {
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
		if ($objects != null) {
			$notification->setObjects($objects);
		}
		if ($objects_id != null) {
			$notification->setObjectsId($objects_id);
		}		
		$notification->setUser($userextend);
		$notification->setEmail(0);
		$notification->setView(0);
		
		$em->persist($notification);
		$em->flush();
	}
	
	function parseNotification(Notification $notification) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$objects = explode(",", $notification->getObjects());
		$objects_id = $explode(",", $notification->getObjectsId());
		$i = 0;
		foreach($objects as $object) {
			$re = $em->getRepository($object);
			$elements[] = $re->findOneById($objects_id[$i]);
			$i++;
		}
		return $elements;
		// foreach($notification->getObjects() as $not) 
		// TODO: Devolver el texto traducido con los objetos
	}
}