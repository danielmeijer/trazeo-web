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
	 * @Template
	 */
    public function indexAction(Request $request)
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
<<<<<<< HEAD
=======
    	
    	$groupsRide = array();
    	
		        foreach($groupsMember as $groupMember){
		        	if($groupMember->getHasRide() == 0){
		        		$groupsRide[] = $groupMember;
		        	}
        	}
>>>>>>> fcc0532b4224381a6babc8a77758f5c293ec5993

    	$twig_variables = array(
            'user' => $user,
    		'childs' => $childs,
            'groups' => $groups,
    		'routes' => $routes,
    		'notifications' => $not,
<<<<<<< HEAD
    		'groupsMember' => $groupsMember
=======
    		'groupsRide' => $groupsRide
>>>>>>> fcc0532b4224381a6babc8a77758f5c293ec5993
    	);
	    return $this->render('TrazeoFrontBundle:Panel:home.html.twig', $twig_variables);
	}

}