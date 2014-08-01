<?php

namespace Sopinet\Bundle\GamificationBundle\Service;

use Sopinet\Bundle\GamificationBundle\SopinetGamificationBundle;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sopinet\Bundle\GamificationBundle\Entity\EAction;
use Sopinet\Bundle\GamificationBundle\Entity\EUserAction;
use Sopinet\Bundle\GamificationBundle\Entity\ESequence;

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
		else{
			$user=$user->getUser();
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
	 * 
	 */
	function getUserRepository() {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$con = $this->_container;
		$class=$con->parameters['sopinet_gamification.class'];
		return $em->getRepository($class);
	}

	/**
	 * Get difference with a expecific format
	 * @param DateInterval $difference
	 * @param string $format
	 */
	private function _getDifferenceFormated($difference, $format) {
		$df='$val=$difference->'.$format.'; return $val;';
        return eval($df);
	}

	/**
	 * Add action for user logged (or user by parameter)
	 * 
	 * @param String name name of the action
	 * @param User user (optional)
	 * @param Integer value value for acumulative action (optional)
	 * @param String target_entities rute of the entities involved on the action
	 * @param String ids ids of the entities
	 */
	function addUserAction($name,$target_entities = "",$ids = "" ,$user = null, $value = 1,$call_api = false) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$userextend = $this->_getSopinetUserExtend($user);

		$reActions=$em->getRepository("SopinetGamificationBundle:EAction");
		$action=$reActions->findOneByName($name);
		if($action==null)return null;

		$reUserActions = $em->getRepository("SopinetGamificationBundle:EUserAction");
		$userActions= $reUserActions->findBy(array('sopinetuserextends' => $userextend,'actions' => $action,'ids'=>$ids),Array('createdAt' => 'DESC'));

		if (count($userActions) > 0) {
			$lastAction = $userActions[0];
		} else {
			$lastAction = null;
		}


		$addflag=($lastAction==null);	
		if(!$addflag && $action->getAcumulative()){
			$this->_acumulateAction($lastAction,$action,$value,$target_entities,$ids,$user);
			$this->_updateUserPoints($user,$action->getPoints()*$value,$call_api);
			return $lastAction;
		}	
		elseif(!$addflag) $addflag=(!$this->_checkUniqueAction($action,$target_entities,$ids,$userextend) && $this->_timeRestrictionCheck($action,$lastAction));

		if($addflag){
			$useraction = new EUserAction();
			$useraction->setSopinetUserExtends($userextend);
			$useraction->setActions($action);
			$useraction->setEntitiesInvolved($target_entities);
			$useraction->setIds($ids);		
			$useraction->setAcumulated($value);	
			$em->persist($useraction);
			$em->flush();
			$this->_updateUserPoints($userextend,$action->getPoints()*$value,$call_api);
			return $useraction;
		}

		return null;
	}


	/**
	 * Add sequence for user logged (or user by parameter)
	 * 
	 * @param String $name name of the sequence
	 * @param User $user (optional)
	 */
	function addSequence($name, $user = null) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$userextend = $this->_getSopinetUserExtend($user);

		$reSequences=$em->getRepository("SopinetGamificationBundle:ESequence");
		$sequence=$reSequences->findOneByName($name);
		if($sequence==null)return null;

		if($this->_checkSequence($sequence,$userextend)){
			$useraction = new EUserAction();
			$useraction->setSopinetUserExtends($userextend);
			$useraction->setSequence($sequence);
			$em->persist($useraction);
			$em->flush();	
			return $useraction;

		}
		return null;
	}


	/**
	 * Update sequences for user logged (or user by parameter)
	 * 
	 * @param User $user (optional)
	 */
	function updateSequences($user = null) {
		$em = $this->_container->get("doctrine.orm.entity_manager");		
		$userextend = $this->_getSopinetUserExtend($user);

		$reSequences=$em->getRepository("SopinetGamificationBundle:ESequence");
		$sequences=$reSequences->findAll();

		$reUserActions = $em->getRepository("SopinetGamificationBundle:EUserAction");
		$userActions= $reUserActions->findBy(array('sopinetuserextends' => $userextend));

		if (count($userActions) == 0)return null;

		$newSequences=[];
		foreach ($sequences as $sequence) {
				$added=$this->addSequence($sequence->getName(),$userextend);
				if($added!=null)array_push($newSequences, $added);
		}
		return $newSequences;
	}

	/**
	 * Update User Points
	 */
	private function _updateUserPoints($user =null, $points=0, $call_api) {
		$em = $this->_container->get("doctrine.orm.entity_manager");	
		$userextend = $this->_getSopinetUserExtend($user);
		$reUserActions = $this->getUserRepository();
		$sopinetuserextend=$user->getUser()->getUserExtend();
		$sopinetuserextend->setPoints((integer)$this->getUserPoints($user));
		$em->persist($sopinetuserextend);
		$em->flush();	
		$con = $this->_container;
		$api = $con->parameters['sopinet_gamification.api'];
		if($call_api)$this->_callApi($api,$user->getUser(),$points);
	}

	/**
	 * Get User Points
	 */
	function getUserPoints($user =null) {
		$em = $this->_container->get("doctrine.orm.entity_manager");		
		$userextend = $this->_getSopinetUserExtend($user);

		$reUserActions = $em->getRepository("SopinetGamificationBundle:EUserAction");
		$userActions= $reUserActions->findBy(array('sopinetuserextends' => $userextend));

		$points=0;
		foreach ($userActions as $userAction) {
			if($userAction->getActions()!=null){
				$points+=ceil($userAction->getActions()->getPoints()*$userAction->getAcumulated());
			}
			elseif($userAction->getSequence()!=null)$points+=$userAction->getSequence()->getPoints();
		}
		return $points;
	}


	/**
	 * Get User Actions
	 */
	function getUserActions($user =null) {
		$em = $this->_container->get("doctrine.orm.entity_manager");		
		$userextend = $this->_getSopinetUserExtend($user);

		$reUserActions = $em->getRepository("SopinetGamificationBundle:EUserAction");
		$userActions=$reUserActions->findBy(array('sopinetuserextends' => $userextend));

		return array('actions' => $userActions);
	}

	/**
	 * Increase an acumulative action
	 *
	 * @param UserAction lastAction
	 * @param Action action
	 * @param Integer $value value to acumulate
	 * @return Action
	 */
	private function _acumulateAction($lastAction,$action,$value,$target_entities,$ids,$user){
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$now=new \DateTime();
		$difference =  $now->diff($lastAction->getCreatedAt());
		$format=explode(",", $action->getTimeRestriction())[0];
		$diffFormated=$this->_getDifferenceFormated($difference,$format);

		$acumulated=$lastAction->getAcumulated();
		$user=$this->_getSopinetUserExtend($user);
		//si ya se ha excedido el tiempo de acumulación estipulado se crea una nueva accion
		if($diffFormated>=explode(",", $action->getTimeRestriction())[1]){
			$useraction = new EUserAction();
			$useraction->setActions($action);
			$useraction->setSopinetUserExtends($user);
			//se comprubeba si con el incremento seria mayor que el limite
			if($value*$action->getPoints()<$action->getPointsRestriction())$useraction->setAcumulated($value);
			else $useraction->setAcumulated($action->getPointsRestriction());
			$useraction->setEntitiesInvolved($target_entities);
			$useraction->setIds($ids);
			$em->persist($useraction);
			$em->flush();	
			return $useraction;
		}

		//si no se comprueba que no se halla excedido el limite de puntos en este periodo
		elseif(($acumulated*$action->getPoints())<$action->getPointsRestriction()){
			$points_acumulated=($lastAction->getAcumulated()+$value)*$action->getPoints();
			//se comprubeba si con el incremento seria mayor que el limite
			if($points_acumulated<$action->getPointsRestriction())$lastAction->setAcumulated($acumulated+$value);
			else $lastAction->setAcumulated($action->getPointsRestriction()/$action->getPoints());
			$em->persist($lastAction);
			$em->flush();	
			return $lastAction;
		}

		return $lastAction;
	}

		
	/**
	 * Check restriction time of an action
	 *
	 * @param UserAction lastAction
	 * @param Action action
	 * @param Integer $value value to acumulate
	 * @return Action
	 */	
	private function _timeRestrictionCheck($action,$lastAction){
		$difference = $lastAction->getCreatedAt()->diff(new \DateTime('now'));
		$format=explode(",", $action->getTimeRestriction())[0];
		$diffFormated=$this->_getDifferenceFormated($difference,$format);
		return($diffFormated>=explode(",", $action->getTimeRestriction())[1]);
	}
		
	/**
	 * Check if conditions for a sequence are archieved
	 *
	 * @param Sequence sequence
	 * @param UserExtend userextend
	 * @return Boolean
	 */	
	private function _checkSequence($sequence,$userextend){
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$reUserActions = $em->getRepository("SopinetGamificationBundle:EUserAction");
		$existSequence=$reUserActions->findBySequence($sequence);
		if($existSequence==null)return false;

		$userActions= $reUserActions->findBy(array('sopinetuserextends' => $userextend, 'actions' => $sequence->getActions()));
		
		$groups=$this->_groupActionsByEntitiesInvolved($userActions);

		foreach ($groups as $group) {
			if(count($group)>=count($sequence->getActions()))return true;
		}
		
		return false;
	}

	/**
	 * Check if actions had been tiggered by the same entities 
	 *
	 * @param Array actions
	 * @return Boolean
	 */	
	private function _groupActionsByEntitiesInvolved($userActions){
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$groups=[];
		//TODO para varias entidades
		$reTargetEntities=$em->getRepository(explode(',',$useraction->getEntitiesInvolved())[0]);
		foreach ($userActions as $useraction) {
			if(count($groups[0])!=0){
				$found=false;
				foreach ($groups as $group) {
					if($reTargetEntities->findById(explode(',',$group[0]->getIds())[0])
						==$reTargetEntities->findById(explode(',',$useraction->getIds())[0]))
					{
						array_push($group, $useraction);
						$found=true;
					}

				}
				if(!$found){
					end($groups);
					array_push($groups[key($groups)], $useraction);
				}

			}
			else array_push($groups[0], $useraction);
		}
		return $groups;
	}
		
	/**
	 * Check if conditions for a sequence are archieved
	 *
	 * @param Action action
	 * @param Array userActions
	 * @return Boolean
	 */	
	private function _checkUniqueAction($action,$target_entities,$ids,$userextend){
		if($action->getUnique()||$target_entities=="")return true;

		$em = $this->_container->get("doctrine.orm.entity_manager");
		$reUserActions = $em->getRepository("SopinetGamificationBundle:EUserAction");		
		$existAction=$reUserActions->findBy(array('sopinetuserextends' => $userextend, 'ids' => $ids));		
		return ($existAction==null);
	}

	/**
	 * Obtains target entities's action _tostring
	 *
	 * @param UserAction
	 * @return Array strings with the _tostring of the target entities
	 */
	public function actionToString($action){
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$reEntities=explode(",", $action->getEntitiesInvolved());
		$ids=explode(",", $action->getIds());
		$objects=[];
		for ($i=0; $i < count($ids); $i++) { 
			$re=$em->getRepository($reEntities[$i]);
			$name=$re->findOneById($ids[$i]);
			array_push($objects, $name);
		}	
		array_push($objects, $action->getAcumulated());
		return $objects;
	}

	/**
	 * Call Api
	 * @param String api to call
	 */
	private function _callApi($api =null,$user,$points) {
		switch ($api) {
			case 'CiviClub':
				$con=$this->_container;
				$con = $con->get('sopinet_gamification_civiclub');
				$con->civiclubCall($user,$points);
				break;
			
			default:
				break;
		}

	}
}