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
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\BaseBundle\Entity\EEvent;
use Trazeo\BaseBundle\Entity\EReport;
use Trazeo\BaseBundle\Entity\EGroup;
use Trazeo\BaseBundle\Entity\EGroupAccess;
use Sopinet\Bundle\SimplePointBundle\ORM\Type\SimplePoint;
use Sopinet\TimelineBundle\Entity\Comment;

class ApiController extends Controller {
	
	/**
	 * Funcion para representar un uso erroneo de la API
	 */
	private function msgDenied($msg=null) {
		$array['state'] = -1;
		if($msg!=null)$array['msg'] = $msg;
		else $array['msg'] = "Access Denied";
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
	 * @param Request request
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
		//Se escogen los grupos del usuario, si la llamada se hizo con el parametro admin
		// solo se devolveran aquellos en los que es admin 
		$groups = $userextend->getGroups();
		$admingroups=$em->getRepository('TrazeoBaseBundle:EGroup')->findByAdmin($userextend);

		$array = array();
		foreach($groups as $group){
			$arrayGroups = array();
			$arrayGroups['id'] = $group->getId();
			$arrayGroups['name'] = $group->getName();
			$arrayGroups['visibility'] = $group->getVisibility();
			$arrayGroups['hasride'] = $group->getHasRide();
			if(in_array($group, $admingroups))$arrayGroups['admin']=true;
			else $arrayGroups['admin']=false;
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
				// Comprobar si hay permisos para Crear el Paseo
				$groupsIds = array();
				$groupsIds[] = 63;
				$groupsIds[] = 21;
				$groupsIds[] = 20;
				$groupsIds[] = 19;
				$groupsIds[] = 18;
				$groupsIds[] = 37;

				$emailsToFilter = array();
				$emailsToFilter[] = "prudennl92@gmail.com";
				$emailsToFilter[] = "fermincabal94@gmail.com";
				$emailsToFilter[] = "victornogpan@gmail.com";
				$emailsToFilter[] = "clsouton@gmail.com";
				$emailsToFilter[] = "laura.alberquilla@gmail.com";
				$emailsToFilter[] = "lrodrigosanchez@gmail.com";
				$emailsToFilter[] = "gemi87.jg@gmail.com";
				$emailsToFilter[] = "elenacarrie@gmail.com";
				
				$canInitRide = true;
				if (in_array($id_group, $groupsIds)) {
					$canInitRide = false;
					if (in_array($user->getEmail(), $emailsToFilter)) {
						$canInitRide = true;
					}
				}
				if (!$canInitRide) {
					$array['id_ride'] = "-1"; // No tiene permisos para iniciar el paseo
					
					$view = View::create()
					->setStatusCode(200)
					->setData($this->doOK($array));
					
					return $this->get('fos_rest.view_handler')->handle($view);					
				}
				
				
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
        		false
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
	 * @POST("/api/register")
	 * @param Request request
	 */
	public function postRegisterAction(Request $request)
    {
    	//recabamos los datos de la peticion
    	$username = $request->get('username');
		$password = $request->get('password');
		//se comprueba si el usuario existe
    	$em = $this->get('doctrine.orm.entity_manager');
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByNick($username);
		if($userextend!=null){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied("Email is already in use"));
		
			return $this->get('fos_rest.view_handler')->handle($view);		
		}
		//se crea el usuario
		$userManager = $this->container->get('fos_user.user_manager');
      	$newUser = $userManager->createUser();
      	$newUser->setUsername($username);
      	$newUser->setUsernameCanonical($username);
      	$newUser->setPassword($password);
      	$newUser->setEmail($username);
      	$newUser->setEnabled(true);
      	$em->persist($newUser);
      	$em->flush();
      	// Creamos el correo de bienvenida
	    $message = \Swift_Message::newInstance()
	    // TODO: Traducir
	    ->setSubject("Bienvenido a Trazeo.")
	    ->setFrom(array("hola@trazeo.es" => "Trazeo"))
	    ->setTo($newUser->getEmail())
	    ->setBody($this->get('templating')->render('SopinetTemplateSbadmin2Bundle:Emails:newUser.html.twig', array()), 'text/html');
	    $ok = $this->get('mailer')->send($message);   
      	//se devuelve el id del usuario
      	$array['id'] = $newUser->getId();
        $view = View::create()
			->setStatusCode(201)
			->setData($this->doOK($array));
        return $this->get('fos_rest.view_handler')->handle($view);
    }

	/**
	 * Petición para crear un grupo en la bbdd
	 * @POST("/api/manageGroup")
	 */
	public function manageGroupAction(Request $request) {
	
		$name = $request->get('name');
		$visibility = $request->get('visibility');
		$id_group= $request->get('id_group');

		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
	
		$em = $this->get('doctrine.orm.entity_manager');
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);

		//Se comprueba si el nombre del grupo ya existe 		
		$group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneByName($name);
		if( $group!=null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied("Name is already in use"));
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}

		//Si el grupo existe se modifica si no se crea uno nuevo
		$group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneBy(array('id'=>$id_group,'admin'=>$userextend));

		//Si el usuario no es el admin del grupo y lo intenta modificar se deniega el ascesso
		if($id_group!=null && $group==null){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied("User is not the admin"));
	
			return $this->get('fos_rest.view_handler')->handle($view);			
		}

		//Si el grupo no existe se crea
		else if($group==null){
			$group= new EGroup();
	        $group->addUserextendgroup($userextend);            
	        $group->setAdmin($userextend);
	        //Children autojoin on parent create group
	        $childs=$userextend->getChilds();
  	      	foreach($childs as $child){
   	        	$group->addChild($child);
	        }
	      	//Add points for create frist group
        	$sopinetuserextend=$em->getRepository("SopinetUserBundle:SopinetUserExtend")->findOneByUser($userextend);
        	$container = $this->get('sopinet_gamification');
        	$container->addUserAction(
        	"Create_Group",
        	"TrazeoBaseBundle:UserExtend",
        	$userextend->getId(),
        	$userextend,
        	1,
        	false
        	);
		}
        $group->setName($name);
        $group->setVisibility($visibility);
        $em->persist($group);
        $em->flush();

        //Se devuelve el id del grupo
        $data['id'] = $group->getId();
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($data));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	
	}

	/**
	 * @POST("/api/manageChild")
	 * @param Request request
	 */
	public function manageChildrenAction(Request $request) {

		$id_child= $request->get('id_child');	
		$name = $request->get('name');
		$school = $request->get('school');
		$date = $request->get('date');
		$gender= $request->get('gender');//boy girl

		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
		$em = $this->get('doctrine.orm.entity_manager');
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
		//Si el niño existe se modifica si no se crea uno nuevo
		$child = $em->getRepository('TrazeoBaseBundle:EChild')->findOneById($id_child);
		$tutor= in_array($child, $userextend->getChilds()->toArray());	

		$new=false;//flag que indica si el niño se va a crear como nuevo
		//Si el usuario no es el tutor del niño y lo intenta modificar se deniega el ascesso
		if($id_child!=null && $tutor==false){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied("User is not the tutor"));
	
			return $this->get('fos_rest.view_handler')->handle($view);			
		}
		//Si el niño no existe se crea
		else if($child==null){
			$child=new EChild();
			$new=true;
		}

		if($date)$child->setDateBirth(new \DateTime($date));
		else $child->setDateBirth(new \DateTime());
		if($school)$child->setScholl($school);
		if($new)$child->addUserextendchild($userextend); 
		$child->setSelected(false);
		$child->setGender($gender);
        $child->setNick($name);		
		$child->setVisibility(true);

        $em->persist($child);
        $em->flush();

        if($new){
        	$userextend->addChild($child);
   	        $em->persist($userextend);
	        $em->flush();
        }
        //Se devuelve el id del niño
        $data['id'] = $child->getId();
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($data));
		return $this->get('fos_rest.view_handler')->handle($view);
	}

	/**
	 * @GET("/api/groupsCity")
	 */
	public function getGroupsByCityAction(Request $request) {
		$city = $this->getRequest()->get('city');
		$object=$this->getRequest()->get('object');
		$em = $this->get('doctrine.orm.entity_manager');
		$user = $this->checkPrivateAccess($request);
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);

		$helper = $this->get('trazeo_base_helper');
		$cities = $helper->getCities($city);

		$admingroups=$em->getRepository('TrazeoBaseBundle:EGroup')->findByAdmin($userextend);

		$names=[];
		//Para todas las ciudades
		if($city=='all'){
			$groups=$em->getRepository('TrazeoBaseBundle:EGroup')->findAll();
			foreach ($groups as $group) {
				if(!$object){
					$names['data']['groups'][]=$group->getName();
					$names['data']['ids'][]=$group->getId();
				}
				else{
					$arrayGroups = array();
					$arrayGroups['id'] = $group->getId();
					$arrayGroups['name'] = $group->getName();
					if($group->getRoute()!=null){
						$arrayGroups['route']['name']=$group->getRoute()->getName();
						$arrayGroups['route']['admin_name']=$group->getAdmin()->__toString();
					}
					$arrayGroups['visibility'] = $group->getVisibility();
					$arrayGroups['hasride'] = $group->getHasRide();
					if(in_array($group, $admingroups))$arrayGroups['admin']=true;
					else $arrayGroups['admin']=false;
					if($group->getHasRide()==1){
						$ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneByGroup($group);
						$arrayGroups['ride_id']=$ride->getId();
					}
					$names['data'][] = $arrayGroups;
				}
			}
			$response = json_encode($names);

			return new Response($response, 200, array(
    	        'Content-Type' => 'application/json'
        	));
		}
		//solo para la ciudad indicada
		$routes = $em->getRepository('TrazeoBaseBundle:ERoute')->findByCity($cities[0]);
		foreach ($routes as $route) {
					$group=$em->getRepository('TrazeoBaseBundle:EGroup')->findOneByRoute($route);
					if($group!=null && !$object){
					$names['data']['groups'][]=$group->getName();
					$names['data']['ids'][]=$group->getId();
					}
					else if($group!=null){
						$arrayGroups = array();
						$arrayGroups['id'] = $group->getId();
						$arrayGroups['name'] = $group->getName();
						if($group->getRoute()!=null){
							$arrayGroups['route']['name']=$group->getRoute()->getName();
							$arrayGroups['route']['admin_name']=$group->getAdmin()->__toString();
						}
						$arrayGroups['visibility'] = $group->getVisibility();
						$arrayGroups['hasride'] = $group->getHasRide();
						if(in_array($group, $admingroups))$arrayGroups['admin']=true;
						else $arrayGroups['admin']=false;
						if($group->getHasRide()==1){
						$ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneByGroup($group);
						$arrayGroups['ride_id']=$ride->getId();
						}
						$names['data'][] = $arrayGroups;
					}
		}

		$response = json_encode($names);

		return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));
	}

	/**
	 * @GET("/api/cities")
	 */
	public function getCitiesAction(Request $request) {
		$user = $this->checkPrivateAccess($request);
		$em = $this->get('doctrine.orm.entity_manager');
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);

		$reJJ = $em->getRepository("JJsGeonamesBundle:City");
		$routes = $em->getRepository('TrazeoBaseBundle:ERoute')->findAll();
		
		$cities=[];
		if($userextend)$userCity=$userextend->getCity();
		else $userCity=null;
		$userCity=$reJJ->findOneById($userCity);
		if($userCity!=null)$userCity=$userCity->getNameUtf8();
		$info=[];
		foreach ($routes as $route) {
					$city=$reJJ->findOneById($route->getCity());
					if($city!=null && !in_array($city->getNameUtf8(),$cities))$cities[]=$city->getNameUtf8();
		}
		$info['cities']=$cities;
		$info['userCity']=$userCity;
		$response = json_encode($info);

		return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));
	}
	/**
	 * @POST("/api/joinGroup")
	 */
	public function joinGroupAction(Request $request) {
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
		$id_group= $request->get('id_group');

		$em = $this->getDoctrine()->getManager();
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
		$group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($id_group);

		if( !$group){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied("Group doesn't exist"));
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}		

		$userextends=$group->getUserextendgroups()->toArray();

		if (in_array($userextend, $userextends)) {
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied("User it's already on group"));
			return $this->get('fos_rest.view_handler')->handle($view);
		}
		$groupAdmin = $group->getAdmin();
		$groupVisibility = $group->getVisibility();
		$info=array();
		if($groupAdmin == $user || $groupVisibility == 0){
				
			$group->addUserextendgroup($userextend);
			$em->persist($group);
			$em->flush();
			
			//Children autojoin on parent join to group 
			$childs=$userextend->getChilds();
			foreach($childs as $child){
				$group->addChild($child);
			}
			$em->persist($group);
			$em->flush();
			
			$info['joined']='true';
			$response = json_encode($info);
			return new Response($response, 200, array(
   	        	'Content-Type' => 'application/json'
       		));					
		}
		$view = View::create()
		->setStatusCode(200)
		->setData($this->msgDenied("The group is not public"));
	
		return $this->get('fos_rest.view_handler')->handle($view);	
	}

	/**
	 * @POST("/api/requestJoinGroup")
	 */
	public function requestJoinGroupAction(Request $request) {
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
		$id= $request->get('id_group');	
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);

		$userId = $user->getId();

		// Obtener grupo al que se quiere unir a través del param $id
		$group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($id);
		if( !$group){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied("Group doesn't exist"));
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}		
		$groupId = $group->getId();
		
		$groupVisibility = $group->getVisibility();
		// Buscar si existe alguna petición con ese UserExtend y ese Group
		$requestUser = $em->getRepository('TrazeoBaseBundle:EGroupAccess')->findOneByUserextend($user);
		$requestGroup = $em->getRepository('TrazeoBaseBundle:EGroupAccess')->findOneByGroup($group);
		$container = $this->get('sopinet_flashMessages');
		if ($groupVisibility == 2 ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied("The group is hidden"));
			return $this->get('fos_rest.view_handler')->handle($view);				
		}
		else if($groupVisibility == 0){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied("The group is public"));
			return $this->get('fos_rest.view_handler')->handle($view);				
		}
		// Comprobar que existen
		if($requestUser && $requestGroup == true){
			
			// Si existen, obtener el id de su registro en la base de datos
			$requestUserId = $requestUser->getId();
			$requestGroupId = $requestGroup->getId();
			// Comprobar que no tienen el mismo id de registro (petición duplicada)
			if($requestUserId = $requestGroupId) {
				// Excepción y redirección
				$view = View::create()
				->setStatusCode(200)
				->setData($this->msgDenied("Join to group request has been did before"));
				return $this->get('fos_rest.view_handler')->handle($view);				
			}
			
		}
		else{
		// Si no existen los UserExtend y Group anteriormente obtenidos,
		// directamente se crea la petición			
			$groupAdmin = $group->getAdmin();
			$groupAdminUser = $em->getRepository('TrazeoBaseBundle:UserExtend')->find($groupAdmin);
			
			$fos_user_admin = $groupAdminUser->getUser();
			//ldd($fos_user_admin);
			$not = $this->container->get('sopinet_user_notification');
			$el = $not->addNotification(
					'group.join.request',
					"TrazeoBaseBundle:UserExtend,TrazeoBaseBundle:EGroup",
					$userId . "," . $groupId,
					$this->generateUrl('panel_group'),$groupAdminUser->getUser()
			);
			
			$access = new EGroupAccess();
			$access->setGroup($group);
			$access->setUserextend($user);
				
			$em->persist($access);
			$em->flush();
			$info['request']='true';
			$response = json_encode($info);
			return new Response($response, 200, array(
   	        	'Content-Type' => 'application/json'
       		));						

			
		}

	}

	/**
	 * @POST("/api/user/childs")
	 */
	public function getUserChildrensAction(Request $request) {
	
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
	
		$em = $this->get('doctrine.orm.entity_manager');
	
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
		
		$childs = $userextend->getChilds();
		
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($childs));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	
	}
	
	/**
	 * @POST("/api/group/invite")
	 */
	public function groupInviteAction(Request $request) {
		// TODO: PASAR FUNCION A UN SERVICIO; Se está usando en ApiController y PanelGroupsController
		// TODO: Hay que arreglar esta función, no devuelve un JSON correctamente..
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