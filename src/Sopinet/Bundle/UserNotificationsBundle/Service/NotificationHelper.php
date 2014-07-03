<?php

namespace Sopinet\Bundle\UserNotificationsBundle\Service;

use Sopinet\Bundle\UserNotificationsBundle\SopinetUserNotificationsBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sopinet\Bundle\UserNotificationsBundle\Entity\Notification;

class NotificationHelper {
	private $_container;
				
	function __construct(ContainerInterface $container) {
		$this->_container = $container;
	}
	
	/**
	 * Create SopinetUserExtend by default
	 * @param User $user
	 */
	private function _getSopinetUserExtend($user = null) {
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
		return $userextend;
	}

	/**
	 * Add notification for user logged (or user by parameter)
	 * 
	 * @param String $action
	 * @param String $objects (optional)
	 * @param Integer $objects_id (optional)
	 * @param String $link (optional)
	 * @param User $user (optional)
	 */
	function addNotification($action, $objects = null, $objects_id = null, $link = null, $user = null, $image = null) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		
		$userextend = $this->_getSopinetUserExtend($user);

		//$reNotification = $em->getRepository("SopinetUserNotificationsBundle:Notification");
		$notification = new Notification();
		$notification->setAction($action);
		if ($objects != null) {
			$notification->setObjects($objects);
		}
		if ($objects_id != null) {
			$notification->setObjectsId($objects_id);
		}
		if ($link != null) {
			$notification->setLink($link);
		}
		if ($image != null) {
			$notification->setImage($image);
		}
		$notification->setUser($userextend);
		$notification->setEmail(0);
		$notification->setView(0);
		
		$em->persist($notification);
		$em->flush();
		
		return $notification;
	}
	
	/**
	 * Get String parsed (translated) from Notification
	 * 
	 * @param Notification $notification
	 */
	function parseNotification(Notification $notification, $action = "description") {
		//ldd($notification);
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$objects = explode(",", $notification->getObjects());
		$objects_id = explode(",", $notification->getObjectsId());
		$i = 0;
		foreach($objects as $object) {
			$re = $em->getRepository($object);
			$elements['%'.$i] = $re->findOneById($objects_id[$i]);
			$i++;
		}
		return $this->_container->get('translator')->trans('Notifications.action.'.$action.".".$notification->getAction(), $elements);
	}
	
	/**
	 * Get Notifications from user
	 * 
	 * @param User $user
	 * @param Integer $limit (if it is 0, return ilimited notifications)
	 * @return Array Notifications
	 */
	function getNotifications($user = null, $limit = 5) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$reNotifications = $em->getRepository("SopinetUserNotificationsBundle:Notification");
		
		$userextend = $this->_getSopinetUserExtend($user);
		if ($limit > 0) {
			$notifications = $reNotifications->findBy(array(
					'user' => $userextend,
					'view' => 0
					),array('id' => 'DESC'),$limit);
		} else {
			$notifications = $reNotifications->findBy(array(
					'user' => $userextend,
					'view' => 0
				));
		}
		return $notifications;
		// Devolvemos las notificaciones
	}
	
	
	/**
	 * Get All Notifications from user
	 *
	 * @param User $user
	 * @return Array Notifications
	 */
	function getAllNotifications($user = null) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$reNotifications = $em->getRepository("SopinetUserNotificationsBundle:Notification");
	
		$userextend = $this->_getSopinetUserExtend($user);
			$notifications = $reNotifications->findBy(array(
					'user' => $userextend,
					'view' => -1
			),array('id' => 'DESC'));
		return $notifications;
		// Devolvemos todas las notificaciones
	}
	
	
	function clearNotifications($user = null) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$reNotifications = $em->getRepository("SopinetUserNotificationsBundle:Notification");
		
		$userextend = $this->_getSopinetUserExtend($user);
		$notifications = $reNotifications->findBy(array(
				'user' => $userextend,
				'view' => 0
		));
		$count = 0;
		foreach($notifications as $notification) {
			$count++;
			$notification->setView(1);
			$em->persist($notification);			
			$em->flush();
		}
		return $count;
	}
}