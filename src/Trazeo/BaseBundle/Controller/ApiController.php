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
use Trazeo\BaseBundle\Entity\ERide;
use Trazeo\BaseBundle\Entity\EEvent;
use Trazeo\BaseBundle\Entity\EReport;
use Sopinet\Bundle\SimplePointBundle\ORM\Type\SimplePoint;

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
		//$user = $this->get('security.context')->getToken()->getUser();
	
		/*if ($user != null && $user != "anon.") {
			return $user;
		}*/
	
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
		$array = array();
		foreach($groups as $group){
			$arrayGroups = array();
			$arrayGroups['id'] = $group->getId();
			$arrayGroups['name'] = $group->getName();
			$arrayGroups['visibility'] = $group->getVisibility();
			
			$array[] = $arrayGroups;
		}
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($array));
		
		return $this->get('fos_rest.view_handler')->handle($view);
	}
	
	/**
	 * @POST("/api/ride/createNew")
	 */
	public function getCreateNewRideAction(Request $request) {
	
		//Comprobar si el ride asociado al grupo está creado(hasRide=1)
		$id_group = $request->get('id_group');
		
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
	
		$em = $this->get('doctrine.orm.entity_manager');
		
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
		
		$group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneBy(array("id" => $id_group, "admin" => $userextend->getId()));
		//ldd($group);
		// Si el grupo tiene Paseo asociado, devuelve el paseo
		if($group->getHasRide() == 1 && $group->getRide() != null){
			$array['id_ride'] = $group->getRide()->getId();
			
			$view = View::create()
			->setStatusCode(200)
			->setData($this->doOK($array));
			
			return $this->get('fos_rest.view_handler')->handle($view);
		}
		// Sino, se crea un paseo y se asocia al grupo
		else{ 
			$ride = new ERide();
			//TODO: En la relación Group-Ride, evitar los dos set
			$ride->setGroup($group);
			$ride->setTimeIni(new \DateTime());
			$ride->setGo(1);
			$em->persist($ride);
			$group->setHasRide(1);
			$group->setRide($ride);
			$em->persist($group);
			$em->flush();
			
			$array['id_ride'] = $group->getRide()->getId();
			
			$view = View::create()
			->setStatusCode(200)
			->setData($this->doOK($array));
			
			return $this->get('fos_rest.view_handler')->handle($view);
		}
		
	}
	
	/**
	 * @POST("/api/ride/data")
	 */
	public function getDataRideAction(Request $request) {
	
		$id_ride = $request->get('id_ride');
	
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
	
		$em = $this->get('doctrine.orm.entity_manager');
	
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
		
		$ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneById($id_ride);
		
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($ride));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	
	}
	
	/**
	 * Guarda en el servidor la nueva posición del Grupo
	 * @POST("/api/ride/sendPosition")
	 */
	public function getSendPositionRideAction(Request $request) {
	
		$id_ride = $request->get('id_ride');
		$latitude = $request->get('latitude');
		$longitude = $request->get('longitude');
	
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
	
		$em = $this->get('doctrine.orm.entity_manager');
		
		//$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
	
		$ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneById($id_ride);
		
		$event = new EEvent();
		$event->setRide($ride);
		$event->setAction("point");
		$event->setData("(".$latitude.",".$longitude.")");
		//$event->setLocation(new SimplePoint($latitude, $longitude));
		
		$em->persist($event);
		$em->flush();
		
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK("ok"));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	
	}
	
	/**
	 * Guarda en el servidor la nueva posición del Grupo
	 * @POST("/api/ride/sendChildInRide")
	 */
	public function getSendChildInRideAction(Request $request) {
	
		$id_ride = $request->get('id_ride');
		$id_child = $request->get('id_child');
	
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
	
		$em = $this->get('doctrine.orm.entity_manager');
	
		//$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
	
		$ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneById($id_ride);
		$child = $em->getRepository('TrazeoBaseBundle:EChild')->findOneById($id_child);
		$userextends = $child->getUserextendchilds()->toArray();
	
		//Creamos evento de entrada de un niño
		$event = new EEvent();
		$event->setRide($ride);
		$event->setAction("in");
		$event->setData($id_child);
		
		//Registramos al niño dentro del paseo
		$child->setRide($ride);
		
		//Notificamos a sus tutores
		foreach($userextends as $userextend){
			$not = $this->container->get('sopinet_user_notification');
			$not->addNotification(
					"Notify.parents.child.in",
					"TrazeoBaseBundle:Userextend,TrazeoBaseBundle:EChild",
					$userextend->getId() . "," . $child->getId(),
					$this->generateUrl('panel_dashboard'),
					$userextend->getUser()
			);
		}
	
		$em->persist($child);
		$em->persist($event);
		$em->flush();
	
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK("ok"));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	
	}
	
	/**
	 * Guarda en el servidor la nueva posición del Grupo
	 * @POST("/api/ride/sendChildOutRide")
	 */
	public function getSendChildOutRideAction(Request $request) {
	
		$id_ride = $request->get('id_ride');
		$id_child = $request->get('id_child');
	
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
	
		$em = $this->get('doctrine.orm.entity_manager');
	
		//$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
	
		$ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneById($id_ride);
		$child = $em->getRepository('TrazeoBaseBundle:EChild')->findOneById($id_child);
		$userextends = $child->getUserextendchilds()->toArray();
	
		$event = new EEvent();
		$event->setRide($ride);
		$event->setAction("out");
		$event->setData($id_child);
		
		//Eliminamos el niño del paseo
		$child->setRide(null);
		
		$not = $this->container->get('sopinet_user_notification');
		//Notificamos a sus tutores
		foreach($userextends as $userextend){
			$not->addNotification(
					"Notify.parents.child.out",
					"TrazeoBaseBundle:Userextend,TrazeoBaseBundle:EChild",
					$userextend->getId() . "," . $child->getId(),
					$this->generateUrl('panel_dashboard'),
					$userextend->getUser()
			);
		}
		
		$em->persist($child);
		$em->persist($event);
		$em->flush();
	
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK("ok"));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	
	}
	
	/**
	 * @POST("/api/ride/finish")
	 */
	public function getFinishRideAction(Request $request) {
	
		//Comprobar si el ride asociado al grupo está creado(hasRide=1)
		$id_ride = $request->get('id_ride');
	
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
	
		$em = $this->get('doctrine.orm.entity_manager');
	
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
	
		
		$ride = $em->getRepository('TrazeoBaseBundle:ERide')->find($id_ride);
		$group = $ride->getGroup();
		
		//Cálculo del tiempo transcurrido en el paseo
		$inicio = $ride->getTimeIni();
		$fin = new \DateTime();
		
		$diff = $inicio->diff($fin);
		$duration = $diff->h." horas, ".$diff->i." minutos y ".$diff->s." segundos";
		
		$ride->setDuration($duration);
		$ride->setTimeFin($fin);
		$ride->setGo(0);
		$em->persist($ride);

		$em->flush();
		
		$userextends = $group->getUserextendgroups();
		
		$not = $this->container->get('sopinet_user_notification');
		foreach($userextend as $userextends)
		{
			$not->addNotification(
					"Notify.parents.child.out",
					"TrazeoBaseBundle:ERide",
					$ride->getId(),
					$this->generateUrl('panel_dashboard'),
					$userextend->getUser()
			);
		}
			
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK("ok"));
			
		return $this->get('fos_rest.view_handler')->handle($view);
		
	
	}
	
	/**
	 * Guarda en el servidor la nueva posición del Grupo
	 * @POST("/api/ride/report")
	 */
	public function getReportAction(Request $request) {
	
		$id_ride = $request->get('id_ride');
		$texto = $request->get('texto');
		$tipo_de_incidencia = $request->get('tipo_de_incidencia');
	
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
	
		$em = $this->get('doctrine.orm.entity_manager');
	
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
	
		$ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneById($id_ride);
	
		$report = new EReport();
		$report->setText($texto);
		$report->setUserextend($userextend);
		$report->setRide($ride);
		$report->setType($tipo_de_incidencia);

		$em->persist($report);
		$em->flush();
		
		$array['id'] = $report->getId();
		
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($array));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	
	}
}
