<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Trazeo\BaseBundle\Entity\ERide;
use Trazeo\BaseBundle\Entity\EPoints;
use Sopinet\Bundle\SimplePointBundle\ORM\Type\SimplePoint;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\Controller\FOSRestController;


/**
 * PanelRides controller.
 *
 * @Route("/panel/ride")
 */
class PanelRidesController extends FOSRestController
{

    /**
     * 
     * @Route("/{id}/current", name="panel_ride_current")
     * @Method("GET")
     * @Template()
     */
    public function currentAction(ERide $ride)
    {
    	$em = $this->getDoctrine()->getManager();
    	$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');
    	 
    	$events = $reEvent->findBy(array('action' => "point", 'ride' => $ride->getId()), array('createdAt' => 'DESC'));

    	if ($ride->getGroup() == null) {
    		$groupId = $ride->getGroupid();
    	} else {
    		$groupId = $ride->getGroup()->getId();
    	}
    	$group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneById($groupId);
    	$children = $group->getChilds();    	
    	if($group->getRoute() == true){
    		
    		$routeId = $group->getRoute()->getId();
    		
    		$route = $em->getRepository('TrazeoBaseBundle:ERoute')->findOneById($routeId);
    		 
    		if ($route->getCity() != null) {
    			$city = $route->getCity()->getId();
    			$sponsors = $em->getRepository('TrazeoBaseBundle:ESponsor')->findByCity($city);
    		} else {
    			$sponsors = null;
    		}
    		 
    		return array(
    				'route' => $route,
    				'ride' => $ride,
    				'events' => $events,
    				'sponsors' => $sponsors,
    				'children' => $children
    		);
    		
    	}else{
    		return array(
    				'ride' => $ride,
    				'events' => $events
    		);	
    		
    	}	
    }

    /**
     * Get a resume of a closed ride
     *
     * @Route("/{id}/resume", name="panel_ride_resume")
     * @Method("GET")
     * @Template()
     */
    public function resumeAction(ERide $ride)
    {
    	$em = $this->getDoctrine()->getManager();
    	
    	$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');
    	$events = $reEvent->findBy(array('ride' => $ride->getId()), array('createdAt' => 'DESC'));
    
	   	//group that started the ride
    	if ($ride->getGroup() == null) {
    		$groupId = $ride->getGroupid();
    	} else {
    		$groupId = $ride->getGroup()->getId();
    	}
    	$group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneById($groupId);
    	
    	
		//List of childrens joined to the ride 
    	$childrens = [];
    	foreach ($events as $event){
    		if($event->getAction() == "in"){
    			$child=$em->getRepository('TrazeoBaseBundle:EChild')->findOneById(explode("/",$event->getData())[0]);
    			if($child != null && array_search($child,$childrens) == false )array_push($childrens,$child);
    		}
    	}

    	if($group->getRoute() == true){
    
    		$routeId = $group->getRoute()->getId();
    
    		$route = $em->getRepository('TrazeoBaseBundle:ERoute')->findOneById($routeId);
    		 
    		if ($route->getCity() != null) {
    			$city = $route->getCity()->getId();
    			$sponsors = $em->getRepository('TrazeoBaseBundle:ESponsor')->findByCity($city);
    		} else {
    			$sponsors = null;
    		}
    		 
    		return array(
    				'route' => $route,
    				'ride' => $ride,
    				'events' => $events,
    				'sponsors' => $sponsors,
    				'children' => $childrens
    		);
    
    	}else{
    		return array(
    				'ride' => $ride,
    				'events' => $events
    		);
    
    	}
    }
        
    /**
     * Get Last Point.
     *
     * @Route("/{id}/current/lastEvent",name="panel_ride_last")
     * @Method("GET")
     */
    public function lastEventAction(ERide $ride){
    	$em = $this->getDoctrine()->getManager();
    	$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');
    	
    	$events = $reEvent->findBy(array('action' => "point", 'ride' => $ride->getId()), array('createdAt' => 'DESC'));
    	$lastEvent = $events[0];
    	//ldd($lastEvent);
    	//return  new Response(array(
    		//	'lastEvent' => $lastEvent));
    		
    	return new Response($lastEvent->getData());
    	//return new Response("POINT(10 10)");
    }
    
    /**
     * Get Lasts Events.
     *
     * Dado el ID de un evento (lastid), te devuelve todos los eventos.
     *
     * @Route("/{id}/current/lastEvents/{lastid}",name="panel_ride_lasts")
     * @Method("GET")
     */    
    public function lastEventsAction(ERide $ride, $lastid) {
    	$em = $this->getDoctrine()->getManager();
    	$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');
    	
    	$query = $reEvent->createQueryBuilder('e')
    	->where('e.id > :lastid AND e.ride = :ride')
    	->setParameters(array('lastid' => $lastid, 'ride' => $ride))
    	->orderBy('e.createdAt', 'ASC')
    	->getQuery();
    	
    	
		$events = $query->getResult();
		
		$view = view::create()
		->setFormat('json')
		->setStatusCode(200)
		->setData($events);
		
		//return $this->handleView($view);
		
		return $this->get('fos_rest.view_handler')->handle($view);
		/*
		ldd($events);
			
    	$events = $reEvent->findBy(array('id' => "point", 'ride' => $ride->getId()), array('createdAt' => 'DESC'));
    	
    	echo $lastid;
    	echo $ride->getId();
    	die("HOLA");
    	*/
    }
}
