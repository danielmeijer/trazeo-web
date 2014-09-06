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
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Trazeo\BaseBundle\Entity\ERide;
use Trazeo\BaseBundle\Entity\EEvent;
use Trazeo\BaseBundle\Entity\EReport;
use Sopinet\Bundle\SimplePointBundle\ORM\Type\SimplePoint;
use Sopinet\TimelineBundle\Entity\Comment;

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
		//$user= $this->getDoctrine()->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array ("username"=>$email));		
		if ($user == null){
			$user = $this->getDoctrine()->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array ("username"=>$email, "password"=>$password));
			if ($user == null){
				return false;
			}
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
			$arrayGroups['hasride'] = $group->getHasRide();
			if($group->getHasRide()==1){
				$ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneByGroup($group);
				$arrayGroups['ride_id']=$ride->getId();
			}
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
		/*if($latitude==0.0 && $longitude==0.0){
			$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');
    	
    		$events = $reEvent->findBy(array('action' => "point", 'ride' => $id_ride), array('createdAt' => 'DESC'));
    		$lastEvent = $events[0];
    		if($lastEvent){
				$latitude=$lastEvent->getLocation()->getLatitude();
				$longitude=$lastEvent->getLocation()->getLongitude();
    		}
		}*/		
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
		
		$group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneBy(array("id" => $id_group));
		$members = $group->getUserextendgroups()->toArray();
		
		if(in_array($userextend, $members)){

			// Si el grupo tiene Paseo asociado(está en marcha), devuelve el paseo
			if($group->getHasRide() == 1 && $group->getRide() != null){
				$array['id_ride'] = $group->getRide()->getId();
				
				$view = View::create()
				->setStatusCode(200)
				->setData($this->doOK($array));
				
				return $this->get('fos_rest.view_handler')->handle($view);
			}
			// Sino, se crea un paseo y se asocia al grupo
			else{ 				
				//Cerrar paseo asociado a este grupo, si los hubiera
				if($group->getRide() != null) {
					//Sacamos el paseo asociado
					$ride = $group->getRide();
					
					$group->setHasRide(0);
						
					$em->persist($group);
					$em->flush();
					
					//Cálculo del tiempo transcurrido en el paseo
					$inicio = $ride->getCreatedAt();
					$fin = new \DateTime();
						
					$diff = $inicio->diff($fin);
					$duration = $diff->h." horas, ".$diff->i." minutos y ".$diff->s." segundos";
						
					$ride->setDuration($duration);
					$ride->setGroupid($group->getId());
					$ride->setGroup(null);
					$em->persist($ride);
					$em->flush();
						
					$event = new EEvent();
					$event->setRide($ride);
					$event->setAction("finish");
					$event->setData("");
					
					$em->persist($event);
					
					$em->flush();
					
					
					$group->setRide(null);
					
					//desvinculamos a los niños del paseo 
					$childs = $em->getRepository('TrazeoBaseBundle:EChild')->findByRide($ride);
					foreach ($childs as $child){
						$child->setRide(null);
						$child->setSelected(0);
						$em->persist($child);
						
					}
					$em->flush();
					
				}
				
				$ride = new ERide();
				//TODO: En la relación Group-Ride, evitar los dos set
				$ride->setGroup($group);		
				$ride->setUserextend($userextend);		
				$em->persist($ride);
				$group->setHasRide(1);
				$group->setRide($ride);
				$em->persist($group);
				$em->flush();
				
				$userextends = $group->getUserextendgroups()->toArray();
				
				$not = $this->container->get('sopinet_user_notification');
				foreach($userextends as $userextend)
				{
					$not->addNotification(
							"ride.start",
							"TrazeoBaseBundle:EGroup",
							$group->getId(),
							$this->generateUrl('panel_ride_current', array('id' => $ride->getId())),
							$userextend->getUser()
					);
				}
				
				$event = new EEvent();
				$event->setRide($ride);
				$event->setAction("start");
				$event->setData("");
				$event->setLocation(new SimplePoint($latitude, $longitude));
				$em->persist($event);
				
				$array['id_ride'] = $group->getRide()->getId();
				
				$view = View::create()
				->setStatusCode(200)
				->setData($this->doOK($array));
				
				return $this->get('fos_rest.view_handler')->handle($view);
			}
			
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
	 * Guarda en el servidor la nueva posición del Paseo
	 * @POST("/api/ride/sendPosition")
	 */
	public function getSendPositionRideAction(Request $request) {
	
		$id_ride = $request->get('id_ride');
		$latitude = $request->get('latitude');
		$longitude = $request->get('longitude');
		$createdat = $request->get('createdat');

		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
	
		$em = $this->get('doctrine.orm.entity_manager');
		if($latitude==0.0 && $longitude==0.0){
			$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');
    	
    		$events = $reEvent->findBy(array('action' => "point", 'ride' => $id_ride), array('createdAt' => 'DESC'));
    		$lastEvent = $events[0];
    		if($lastEvent!=null){
				$latitude=$lastEvent->getLocation()->getLatitude();
				$longitude=$lastEvent->getLocation()->getLongitude();
    		}
		}	
		//$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
	
		$ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneById($id_ride);
		
		$event = new EEvent();
		$event->setRide($ride);
		$event->setAction("point");
		$event->setLocation(new SimplePoint($latitude, $longitude));
		$event->setCreatedAt(new\DateTime($createdat));
		$event->setData("");
		
		$em->persist($event);
		$em->flush();
		
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($event));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	
	}
	
	/**
	 * Guarda en el servidor la nueva posición del Grupo
	 * @POST("/api/ride/sendChildInRide")
	 */
	public function getSendChildInRideAction(Request $request) {
	
		$id_ride = $request->get('id_ride');
		$id_child = $request->get('id_child');
		$latitude = $request->get('latitude');
		$longitude = $request->get('longitude');
		$createdat = $request->get('createdat');
	
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
	
		$em = $this->get('doctrine.orm.entity_manager');
		if($latitude==0.0 && $longitude==0.0){
			$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');
    	
    		$events = $reEvent->findBy(array('action' => "point", 'ride' => $id_ride), array('createdAt' => 'DESC'));
    		$lastEvent = $events[0];
    		if($lastEvent !=null){
				$latitude=$lastEvent->getLocation()->getLatitude();
				$longitude=$lastEvent->getLocation()->getLongitude();
    		}
		}		
		//$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
	
		$ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneById($id_ride);
		$child = $em->getRepository('TrazeoBaseBundle:EChild')->findOneById($id_child);
		$userextends = $child->getUserextendchilds()->toArray();
	
		//Creamos evento de entrada de un niño
		$event = new EEvent();
		$event->setRide($ride);
		$event->setAction("in");
		$event->setData($id_child."/".$child->getNick());
		$event->setLocation(new SimplePoint($latitude, $longitude));
		$event->setCreatedAt(new\DateTime($createdat));
		$em->persist($event);
		$em->flush();
		
		//Registramos al niño dentro del paseo
		$child->setRide($ride);
		$child->setSelected(1);
		$em->persist($child);
		$em->flush();

		//Obtenemos el id del grupo
		if($ride->getGroup()!=null)$group=$ride->getGroup()->getId();
		else $group=$em->getRepository("TrazeoBaseBundle:EGroup")->findOneById($ride->getGroupid());
		
		//Notificamos a sus tutores
		foreach($userextends as $userextend){
			$not = $this->container->get('sopinet_user_notification');
			$not->addNotification(
					"child.in",
					"TrazeoBaseBundle:EChild,TrazeoBaseBundle:EGroup",
					$child->getId() . "," . $group,
					$this->generateUrl('panel_ride_current', array('id' => $ride->getId())),
					$userextend->getUser()
			);
		}
	
		$array['selected'] = $child->getSelected();
	
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($array));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	
	}
	
	/**
	 * Guarda en el servidor la nueva posición del Grupo
	 * @POST("/api/ride/sendChildOutRide")
	 */
	public function getSendChildOutRideAction(Request $request) {
	
		$id_ride = $request->get('id_ride');
		$id_child = $request->get('id_child');
		$latitude = $request->get('latitude');
		$longitude = $request->get('longitude');
		$createdat = $request->get('createdat');

		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
	
		$em = $this->get('doctrine.orm.entity_manager');
		if($latitude==0.0 && $longitude==0.0){
			$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');
    	
    		$events = $reEvent->findBy(array('action' => "point", 'ride' => $id_ride), array('createdAt' => 'DESC'));
    		$lastEvent = $events[0];
    		if($lastEvent !=null){
				$latitude=$lastEvent->getLocation()->getLatitude();
				$longitude=$lastEvent->getLocation()->getLongitude();
    		}
		}			
		//$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
	
		$ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneById($id_ride);
		$child = $em->getRepository('TrazeoBaseBundle:EChild')->findOneById($id_child);
		$userextends = $child->getUserextendchilds()->toArray();
	
		$event = new EEvent();
		$event->setRide($ride);
		$event->setAction("out");
		$event->setData($id_child."/".$child->getNick());
		$event->setLocation(new SimplePoint($latitude, $longitude));
		$event->setCreatedAt(new\DateTime($createdat));
		$em->persist($event);
		$em->flush();
		
		//Eliminamos el niño del paseo
		$child->setRide(null);
		$child->setSelected(0);
		$em->persist($child);
		$em->flush();

		//Obtenemos el id del grupo
		if($ride->getGroup()!=null)$group=$ride->getGroup()->getId();
		else $group=$em->getRepository("TrazeoBaseBundle:EGroup")->findOneById($ride->getGroupid());

		$not = $this->container->get('sopinet_user_notification');
		//Notificamos a sus tutores
		foreach($userextends as $userextend){
			$not->addNotification(
					"child.out",
					"TrazeoBaseBundle:EChild,TrazeoBaseBundle:EGroup",
					$child->getId() . "," . $group,
					$this->generateUrl('panel_ride_current', array('id' => $ride->getId())),
					$userextend->getUser()
			);
		}
		
		$array['selected'] = $child->getSelected();
	
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($array));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	
	}
	
	/**
	 * Mandar último punto del paseo
	 * @POST("/api/ride/lastPoint")
	 */
	public function getlastPointRideAction(Request $request) {
	
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
		$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');
		
		$ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneById($id_ride);
		// TODO: Lo ideal sería coger el último PUNTO con un REPOSITORY
		$events = $reEvent->findBy(array('action' => "point", 'ride' => $ride->getId()), array('createdAt' => 'DESC'));
	
		if (count($events) > 0) {
			$data = $events[0];
		} else {
			$data = null;
		}
		
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($data));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	
	}
	
	/**
	 * @POST("/api/ride/finish")
	 */
	public function getFinishRideAction(Request $request) {
	
		//Comprobar si el ride asociado al grupo está creado(hasRide=1)
		$id_ride = $request->get('id_ride');
		$latitude = $request->get('latitude');
		$longitude = $request->get('longitude');
		$createdat = $request->get('createdat');

		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
	
		$em = $this->get('doctrine.orm.entity_manager');
		if($latitude==0.0 && $longitude==0.0){
			$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');
    	
    		$events = $reEvent->findBy(array('action' => "point", 'ride' => $id_ride), array('createdAt' => 'DESC'));
    		$lastEvent = $events[0];
    		if($lastEvent !=null){
				$latitude=$lastEvent->getLocation()->getLatitude();
				$longitude=$lastEvent->getLocation()->getLongitude();
    		}
		}	
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
		
		$ride = $em->getRepository('TrazeoBaseBundle:ERide')->find($id_ride);
		$group = $ride->getGroup();
		
		//Cálculo del tiempo transcurrido en el paseo
		$inicio = $ride->getCreatedAt();
		$fin = new \DateTime();
		
		$diff = $inicio->diff($fin);
		$duration = $diff->h." horas, ".$diff->i." minutos y ".$diff->s." segundos";
		
		if ($group == null) {
			$view = View::create()
			->setStatusCode(200)
			->setData($this->doOK("ok"));
				
			return $this->get('fos_rest.view_handler')->handle($view);			
		}
		
		$group->setHasRide(0);
		$em->persist($group);
		
		$ride->setDuration($duration);
		$ride->setGroupid($group->getId());
		$ride->setGroup(null);
		$em->persist($ride);
		
		//desvinculamos a los niños del paseo
		
		$childs = $em->getRepository('TrazeoBaseBundle:EChild')->findByRide($ride);
		foreach ($childs as $child){
			$child->setRide(null);
			$child->setSelected(0);
			$em->persist($child);
		}
		
		$em->flush();
		
		$event = new EEvent();
		$event->setRide($ride);
		$event->setAction("finish");
		$event->setData("");
		$event->setLocation(new SimplePoint($latitude, $longitude));
		$event->setCreatedAt(new\DateTime($createdat));
		$em->persist($event);

		$em->flush();
		

		//add notifications for parents
	 	$userextends = $group->getUserextendgroups();
	 			
	 	$not = $this->container->get('sopinet_user_notification');
	 	$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');

	 	foreach($userextends as $userextend)
	 	{
			$find=false;
				
	 		//Notification just for parents with childrens on ride
	 		$userChilds=$userextend->getChilds();
	 		foreach ($userChilds as $child){
	 			$data=$child->getId().'/'.$child->getNick();
	 				
	 			$query = $reEvent->createQueryBuilder('e')
	 			->where('e.data LIKE :name AND e.ride = :ride AND e.action = :in')
	 			->setParameters(array('name' => '%'.$child->getNick()."%", 'ride' => $ride, 'in'=> 'in'))
	 			->orderBy('e.createdAt', 'ASC')
	 			->getQuery();
	 				
	 			$child=$query->getResult();
	 				
	 			if(count($child)>0){
	 				$not->addNotification(
	 						"ride.finish",
	 						"TrazeoBaseBundle:EGroup",
	 						$group->getId(),
	 						$this->generateUrl('panel_ride_resume',array('id' => $ride->getId())),
	 						$userextend->getUser());
	 				break 1;
	 			}
	 					
	 		}
	 	}
	 		

		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK('ok'));
			
		return $this->get('fos_rest.view_handler')->handle($view);
		
	
	}
	
	/**
	 * Mandar la fecha del servior
	 * @POST("/api/timeStamp")
	 */
	public function getTimeStampAction(Request $request) {
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
        $now=new \Datetime();
 	 
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($now->format('Y-m-d H:i:s')));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	
	}	

	/**
	 * Guarda en el servidor la nueva posición del Grupo
	 * @POST("/api/ride/report")
	 */
	public function getReportAction(Request $request) {
	
		$id_ride = $request->get('id_ride');
		$texto = $request->get('text');
		$latitude = $request->get('latitude');
		$longitude = $request->get('longitude');
		//$tipo_de_incidencia = $request->get('tipo_de_incidencia');
	
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
	
		$em = $this->get('doctrine.orm.entity_manager');
		if($latitude==0.0 && $longitude==0.0){
			$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');
    	
    		$events = $reEvent->findBy(array('action' => "point", 'ride' => $id_ride), array('createdAt' => 'DESC'));
    		$lastEvent = $events[0];
    		if($lastEvent !=null){
				$latitude=$lastEvent->getLocation()->getLatitude();
				$longitude=$lastEvent->getLocation()->getLongitude();
    		}
		}	

		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
	
		$ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneById($id_ride);
	
		$report = new EReport();
		$report->setText($texto);
		$report->setUserextend($userextend);
		$report->setRide($ride);
		//$report->setType($tipo_de_incidencia);

		$em->persist($report);
		$em->flush();
		$event = new EEvent();
		$event->setRide($ride);
		$event->setAction("report");
		$event->setData($report->getId()."/".$texto);
		$event->setLocation(new SimplePoint($latitude, $longitude));
		
		$em->persist($event);
		$em->flush();
		
		$array['id'] = $report->getId();
		
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($array));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	}
	
	/**
	 * Lista los mensajes del TimeLine (Muro) del Grupo
	 * 
	 * @POST("/api/group/timeline/list")
	 * @param Request $request
	 */	
	public function getTimeLineAction(Request $request) {
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
		
			return $this->get('fos_rest.view_handler')->handle($view);
		}
		
		$em = $this->get('doctrine.orm.entity_manager');
		
		$id_group = $request->get('id_group');
		
		$thread = $em->getRepository('SopinetTimelineBundle:Thread')->findOneById($id_group);
		$comments = $em->getRepository('SopinetTimelineBundle:Comment')->findByThread($thread);
		$data = array();
		foreach($comments as $comment) {
			$comment->setAuthorName($comment->getAuthorName());
			$data[] = $comment;
		}
		
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($data));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	}
	
	/**
	 * Crea un nuevo mensaje en el TimeLine (Muro) del Grupo
	 * 
	 * @POST("/api/group/timeline/new")
	 * @param Request $request
	 */
	public function newTimeLineAction(Request $request) {
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
		
			return $this->get('fos_rest.view_handler')->handle($view);
		}
		$em = $this->get('doctrine.orm.entity_manager');
		$id_group = $request->get('id_group');
		$text = $request->get('text');
		
		$group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneById($id_group);
		$thread = $em->getRepository('SopinetTimelineBundle:Thread')->findOneById($id_group);
		
		// Save comment
		$comment = new Comment();
		$comment->setThread($thread);
		$comment->setAuthor($user);
		$comment->setBody($text);
		
		$em->persist($comment);
		$em->flush();
		
		// Send notifications to Users
		$userextends = $group->getUserextendgroups()->toArray();
		$not = $this->container->get('sopinet_user_notification');
		foreach($userextends as $userextend)
		{
			$not->addNotification(
					"timeline.newFromMonitor",
					"TrazeoBaseBundle:EGroup,SopinetTimelineBundle:Comment",
					$group->getId().",".$comment->getId(),
					$this->generateUrl('panel_group_timeline', array('id' => $group->getId())),
					$userextend->getUser()
			);
		}	
		
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($comment));
			
		return $this->get('fos_rest.view_handler')->handle($view);		
	}
	
	/**
	 * @GET("/api/geo/city/list")
	 */
	public function getGeoCitiesAction() {
		$q = $this->getRequest()->get('q');
		
		$helper = $this->get('trazeo_base_helper');
		$cities = $helper->getCities($q);
		
		$view = View::create()
		->setStatusCode(200)
		->setData($cities);
			
		return $this->get('fos_rest.view_handler')->handle($view);		
	}

	/**
	 * @POST("/api/exchange/code")
	 */
	public function exchangeCodeAction() {
		$user = $this->get('security.context')->getToken()->getUser();
		$q = $this->getRequest()->get('code');
		$em = $this->get('doctrine.orm.entity_manager');
		$code=$this->container->getParameter('exchange_code');
	    //Obtener usuarios que tengan marcada la opcion de conexion con civiclub
        $reUserValue = $em->getRepository("SopinetUserPreferencesBundle:UserValue");
        $civiclub_setting = $em->getRepository("SopinetUserPreferencesBundle:UserSetting")->findOneByName("civiclub_conexion");
		if($q==$code){
			$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
			$sopinetuserextend=$em->getRepository("SopinetUserBundle:SopinetUserExtend")->findOneByUser($userextend->getUser());
	    	//Añadimos los puntos por crear el usuario
            $container = $this->get('sopinet_gamification');
        	if($container->addUserAction(
        		"Create_User",
        		"TrazeoBaseBundle:UserExtend",
        		$userextend->getId(),
        		$userextend,
        		1,
        		$reUserValue->getValue($sopinetuserextend, $civiclub_setting)=='yes'  
        	)!=null){
        		$view = View::create()
				->setStatusCode(200)
				->setData($userextend->getPoints());
        	}
        	else{
				$view = View::create()
				->setStatusCode(200)
				->setData("false");
        	}
		}
		else{
			$view = View::create()
			->setStatusCode(200)
			->setData("false");
		}
		return $this->get('fos_rest.view_handler')->handle($view);		
	}
	
	/**
	 * @POST("/api/group/invite")
	 */	
	public function groupInviteAction(Request $request) {
		// TODO: PASAR FUNCION A UN SERVICIO; Se está usando en ApiController y PanelGroupsController
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
		
		
		$em = $this->get('doctrine.orm.entity_manager');
		
		$um = $this->container->get('fos_user.user_manager');
		
		$container = $this->get('sopinet_flashMessages');
		
		$fos_user_current = $user;
		
		$userEmail = $request->get('email_invite');
		$groupId = $request->get('id_group');
		
		$fos_user = $um->findUserByEmail($userEmail);
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
		
		$group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($groupId);
		$groupUsers = $group->getUserextendgroups();
		
		foreach($groupUsers as $groupUser){
			if($user == $groupUser){
		
				$notification = $container->addFlashMessages("warning","El usuario ya forma parte del grupo");
				return $this->redirect($this->generateUrl('panel_group_timeline',array('id'=>$groupId)));
			}
		}
		
		
		if($fos_user != true){
			// Si el usuario no está registrado, habrá que registrarlo
			$reGAI = $em->getRepository('TrazeoBaseBundle:EGroupAnonInvite');
			$reGAI->createNew($group, $userEmail, $fos_user_current, $this);
				
			// $notification = $container->addFlashMessages("warning","El correo electrónico introducido no corresponde a ningún usuario");
			$notification = $container->addFlashMessages("success","Se ha enviado un email al usuario invitándolo al sistema Trazeo y a este grupo.");
			return $this->redirect($this->generateUrl('panel_group_timeline',array('id'=>$groupId)));
		}
		
		if($fos_user == $fos_user_current ){
			$notification = $container->addFlashMessages("warning","No necesitas invitación para unirte a un grupo del que eres administrador");
			return $this->redirect($this->generateUrl('panel_group_timeline',array('id'=>$groupId)));
		}
		
		// Obtener grupo al que se va a unir a través del param $id
		$group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($groupId);
		
		// Buscar si existe alguna petición con ese UserExtend y ese Group
		$requestUser = $em->getRepository('TrazeoBaseBundle:EGroupInvite')->findOneByUserextend($user);
		$requestGroup = $em->getRepository('TrazeoBaseBundle:EGroupInvite')->findOneByGroup($group);
		
		// Comprobar que existen
		if($requestUser && $requestGroup == true){
		
			// Si existen, obtener el id de su registro en la base de datos
			$requestUserId = $requestUser->getId();
			$requestGroupId = $requestGroup->getId();
			// Comprobar que no tienen el mismo id de registro (petición duplicada)
			if($requestUserId = $requestGroupId) {
				// Excepción y redirección
				$notification = $container->addFlashMessages("warning","Ya has invitado a este usuario anteriormente");
				return $this->redirect($this->generateUrl('panel_group_timeline',array('id'=>$groupId)));
					
			}
		
		}else{
			// Si no existen los UserExtend y Group anteriormente obtenidos,
			// directamente se crea la petición
			$user_current = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user_current->getId());
		
			$not = $this->container->get('sopinet_user_notification');
			$el = $not->addNotification(
					'group.invite.user',
					"TrazeoBaseBundle:UserExtend,TrazeoBaseBundle:EGroup",
					$user_current->getId() . "," . $groupId ,
					$this->generateUrl('panel_group'), $fos_user
			);
		
			$access = new EGroupInvite();
			$access->setGroup($group);
			$access->setUserextend($user);
			$access->setSender($user_current);
		
			$em->persist($access);
			$em->flush();
		
			$container = $this->get('sopinet_flashMessages');
			$notification = $container->addFlashMessages("success","El usuario ha recibido tu invitación para unirse al grupo");
			return $this->redirect($this->generateUrl('panel_group_timeline',array('id'=>$groupId)));
		
		}
	}	
}