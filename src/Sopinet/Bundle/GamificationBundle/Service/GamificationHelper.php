<?php

namespace Sopinet\Bundle\GamificationBundle\Service;

use Sopinet\Bundle\GamificationBundle\SopinetGamificationBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sopinet\Bundle\GamificationBundle\Entity\Action;
use Sopinet\Bundle\GamificationBundle\Entity\UserAction;
use Sopinet\Bundle\GamificationBundle\Entity\Sequence;

class GamificationHelper {
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
	 * Add action for user logged (or user by parameter)
	 * 
	 * @param String $name name of the action
	 * @param User $user (optional)
	 */
	function addAction($name, $user = null) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		
		$userextend = $this->_getSopinetUserExtend($user);

		$reActions=$em->getRepository("SopinetGamificationBundle:Action");
		$action=$reActions->findOneByName($name);
		if($action==null)return null;

		$reUserActions = $em->getRepository("SopinetGamificationBundle:UserAction");
		$userActions= $reUserActions->findBy(array('user' => $userextend,'action' => $action),Array('createdAt' => 'DESC'));

		if (count($userActions) > 0) {
			$lastAction = $userActions[0];
		} else {
			$lastAction = null;
		}


		$addflag=($lastAction==null);		
		if(!$addflag && !$action->getUnique() && $this->_timeRestrictionCheck($action,$lastAction)){
			$addflag=true;
		}

		$useraction = new UserAction();
		$useraction->setAction($action);
		$em->persist($useraction);
		$em->flush();
		
		return $useraction;
	}


	/**
	 * Add action for user logged (or user by parameter)
	 * 
	 * @param String $name name of the action
	 * @param User $user (optional)
	 */
	function addAction($name, $user = null) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		
		$userextend = $this->_getSopinetUserExtend($user);

		$reActions=$em->getRepository("SopinetGamificationBundle:Action");
		$action=$reActions->findOneByName($name);
		if($action==null)return null;

		$reUserActions = $em->getRepository("SopinetGamificationBundle:UserAction");
		$userActions= $reUserActions->findBy(array('user' => $userextend,'action' => $action),Array('createdAt' => 'DESC'));

		if (count($userActions) > 0) {
			$lastAction = $userActions[0];
		} else {
			$lastAction = null;
		}


		$addflag=($lastAction==null);		
		if(!$addflag && !$action->getUnique() && $this->_timeRestrictionCheck($action,$lastAction)){
			$addflag=true;
		}

		$useraction = new UserAction();
		$useraction->setAction($action);
		$em->persist($useraction);
		$em->flush();
		
		return $useraction;
	}

	/**
	 * Get User Points
	 */
	function getUserPoints() {
		$em = $this->_container->get("doctrine.orm.entity_manager");		
		$userextend = $this->_getSopinetUserExtend($user);

		$reUserActions = $em->getRepository("SopinetGamificationBundle:UserAction");
		$userActions= $reUserActions->findBy(array('user' => $userextend));
		$points=0;
		foreach ($userActions as $userAction) {
			$points+=$userAction->getAction()->getPoints();
		}
		return $points;
	}
	
	/**
	 * Update sequences for an user
	 * 
	 * @param User $user
	 *
	 * @return Array sequences
	 */
	function updateSequences($user = null) {
		$em = $this->_container->get("doctrine.orm.entity_manager");		
		$userextend = $this->_getSopinetUserExtend($user);

		$reSequences = $em->getRepository("SopinetGamificationBundle:Sequence");
		$userSequences=$reSequences->findAll();

		foreach ($userSequences as $userSequence) {
			$actionsRequire
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