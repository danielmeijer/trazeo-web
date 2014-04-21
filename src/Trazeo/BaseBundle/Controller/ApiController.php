<?php

namespace Trazeo\BaseBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\RouteRedirectView;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Route;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller {
	
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
	 * Funcion para representar un acceso valido a la API
	 * @param array $data Serie de datos
	 * @return array Serie de datos
	 */
	private function doOK($data) {
		$ret['state'] = 1;
		$ret['msg'] = "Ok";
		if($data == null) {
			$arr[] = null;
			$ret['data'] = $arr;
		}
		else
			$ret['data'] = $data;
		return $ret;
	}
	
	/**
	 * Funcion que controla el usuario que envia datos a la API, sin estar logueado, con parámetros email y pass
	 */
	private function checkUser($email, $password){
	
		$user = $this->getDoctrine()->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array ("email"=>$email, "password"=>$password));
	
		if ($user == null){
			return false;
		}
		if ($password == $user->getPassword()){
			return $user;
		}
		else
			return false;
	}
	
	/**
	 * Funcion que controla si el usuario está logueado o se comprueba con su email y pass
	 */
	private function checkPrivateAccess(Request $request) {
		$user = $this->get('security.context')->getToken()->getUser();
	
		if ($user != null && $user != "anon.") {
			return $user;
		}
	
		// TODO: ACTIVAR, QUITAR FALSE / NO HACE FALTA ESTA COMPROBACION
// 		if ('POST' != $request->getMethod() && false) {
// 			return false;
// 		}
	
		$user = $this->checkUser($request->get('email'), $request->get('pass'));
	
		//No es necesario
		if($user == false) {
			return false;
		}
	
		return $user;
	}
	
	/**
	 * @POST("/api/login", name="api_login")
	 */
	public function postLoginAction(Request $request){
		//Se usan anotaciones para comprobar si el método es post
		//if ('POST' == $request->getMethod() || true) {
			$user = $this->checkPrivateAccess($request);
	
			if( $user == false || $user == null ){
				$view = View::create()
				->setStatusCode(200)
				->setData($this->msgDenied());
					
				return $this->get('fos_rest.view_handler')->handle($view);
			}
				
			$array['id'] = $user->getId();
		
			$view = View::create()
			->setStatusCode(200)
			->setData($this->doOK($array));
				
			return $this->get('fos_rest.view_handler')->handle($view);
		//}else
			//return $this->msgDenied();
	}
	
	/**
	 * @POST("/api/groups")
	 */
	public function getGroupsAction(Request $request) {
		
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
		
			return $this->get('fos_rest.view_handler')->handle($view);
		}
		
		$em = $this->get('doctrine.orm.entity_manager');
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
		$groups = $userextend->getGroups();
		
		foreach($groups as $group){
			$arrayGroups = array();
			$arrayGroups['name'] = $group->getName();
			$arrayGroups['visibility'] = $group->getVisibility();
			
			$array[] = $arrayGroups;
		}
		$view = View::create()
		->setStatusCode(200)
		->setData($array);
		
		return $this->get('fos_rest.view_handler')->handle($view);
	}
}
