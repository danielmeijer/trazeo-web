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


/**
 * PanelRides controller.
 *
 * @Route("/panel/ride")
 */
class PanelRidesController extends Controller
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
    	
    	return array(
    		'ride' => $ride,
    		'events' => $events
    	);
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
}
