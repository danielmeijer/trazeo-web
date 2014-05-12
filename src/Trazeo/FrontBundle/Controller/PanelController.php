<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
* @Route("/panel")
*/
class PanelController extends Controller
{
/**
* @Route("/", name="panel_dashboard")
* @Template()
*/
   public function homeAction(Request $request)
   {
	   	$em = $this->getDoctrine()->getManager();
	   	$fos_user = $this->container->get('security.context')->getToken()->getUser();	
	   	$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
	   	
	   	$userId = $user->getId();
	   	$not = $em->getRepository('SopinetUserNotificationsBundle:Notification')->findByUser($userId);
	
	   	$childs = $user->getChilds();
	    $groups = $user->getAdminGroups();
	   	$routes = $user->getAdminRoutes();
	   	$groupsMember = $user->getGroups();
	   	
	   	$groupsRide = array();
	   	
	    foreach($groupsMember as $groupMember){
	    	if($groupMember->getHasRide() == 1){
	       		$groupsRide[] = $groupMember;
	       	}
	    }
	    $tutorial = 0;
	    if(!$user->getTutorial()){
	    	$user->setTutorial(1);
	    	$em->persist($user);
	    	$em->flush();
	    	$tutorial = 1;
	    }
	
	   	return array(
	    			'user' => $user,
	   	 			'childs' => $childs,
	           		'groups' => $groups,
	   	 			'routes' => $routes,
	   	 			'notifications' => $not,
	   	 			'groupsRide' => $groupsRide,
	   				'tutorial' => $tutorial
	   	);
	}

}