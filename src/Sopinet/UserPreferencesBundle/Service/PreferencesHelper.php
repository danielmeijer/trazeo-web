<?php

namespace Sopinet\UserPreferencesBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class PreferencesHelper {
	private $_container;
				
	function __construct(ContainerInterface $container) {
		$this->_container = $container;
	}
	
	function getAllSettings() {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$reSettings = $em->getRepository("SopinetUserPreferencesBundle:UserSetting");
		$settings = $reSettings->findAll();
		return $settings;		
	}
	
	function setAllSettings(Request $request) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$user = $this->_container->get('security.context')->getToken()->getUser();
		$userextend = $user->getSopinetUserExtend();
		
		if ($userextend == null) {
			$userextend = new \Sopinet\UserBundle\Entity\SopinetUserExtend();
			$userextend->setUser($user);
			$em->persist($userextend);
			$em->flush();
		}
		
		$reUserValue = $em->getRepository("SopinetUserPreferencesBundle:UserValue");
		
		foreach($request->request->all() as $key => $value) {
			$temp = explode("_",$key);
			if ($temp[0] == "setting") {
				$usersetting_id = $temp[1];
				$reUserValue->setValue($userextend, $usersetting_id, $value);
			}
		}	
	}
}