<?php

namespace Sopinet\Bundle\GamificationBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\RouteRedirectView;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class CiviClubApi {
	private $_container;
				
	function __construct(ContainerInterface $container) {
		$this->_container = $container;
	}

	/**
	 * Funcion para representar un acceso denegado a la API
	 */
	private function msgDenied() {
		$array['state'] = -1;
		$array['msg'] = "Access Denied";
		return $array;
	}
	
	private function msgOk() {
		$view = view::create()
		->setStatusCode(200)
		->setData($this->doOk(null));
	
		return $this->handleView($view);
	}

	/**
	 * Call to civiclub api
	 *
	 * @param User $user
	 * @param Points $points
	 * @return msg: message to api acces
	 */
	function civiclubCall($user, $points) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$container=$this->_container;
		$gh = $container->get('sopinet_gamification');
		$reUE = $gh->getUserRepository();
		$userextend=$reUE->findOneById($user->getId());

		$security= urlencode($container->getParameter('sopinet_user_civiclub_security'));
		$email=urlencode($user->getEmail());
		$emitter_id=urlencode($container->getParameter('sopinet_user_civiclub_emitter_id'));
		$emitter_center_id=urlencode($container->getParameter('sopinet_user_civiclub_emitter_center_id'));
		$service_id=urlencode($container->getParameter('sopinet_user_civiclub_service_id'));
		$number_uses=urlencode($points);

		$str= "security=".$security."&email=".$email."&emitter_id=".$emitter_id."&emitter_center_id=".$emitter_center_id."&service_id=".$service_id."&number_uses=".$number_uses;

		if($userextend!=null){
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://www.civiclub.org/api/external/point/assign');
			//requesting JSON
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt ($ch, CURLOPT_POST, 1);
 			curl_setopt ($ch, CURLOPT_POSTFIELDS, $str);
 			$response = curl_exec($ch);
 			// If using JSON...
 			$data = json_decode($response);
 			curl_close($ch);
		}
	}	
}