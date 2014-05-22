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
	    $groupsAdmin = $user->getAdminGroups();
	   	$routes = $user->getAdminRoutes();
	   	
	   	$groupsMember = $user->getGroups();
	   	
	   	$allGroups = $em->getRepository('TrazeoBaseBundle:EGroup')->findAll();
	   	$restGroups = array_diff($allGroups,$groupsMember->toArray());
	   	//ldd($restGroups);
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
	           		'groupsAdmin' => $groupsAdmin,
	   	 			'routes' => $routes,
	   	 			'notifications' => $not,
	   	 			'groupsRide' => $groupsRide,
	   				'tutorial' => $tutorial,
	   				'restGroups' => $restGroups,
	   				'groupsMember' => $groupsMember
	   	);
	}

	/**
	* @Route("/doMonitor", name="panel_doMonitor")
	*/
	public function doMonitor() {
		$em = $this->getDoctrine()->getManager();
		$fos_user = $this->container->get('security.context')->getToken()->getUser();
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);

		$user->setUseLike("monitor");
		$em->persist($user);
		$em->flush();
		
		return $this->redirect($this->generateUrl('panel_dashboard'));
	}
	
	/**
	 * @Route("/doUser", name="panel_doUser")
	 */
	public function doUser() {
		$em = $this->getDoctrine()->getManager();
		$fos_user = $this->container->get('security.context')->getToken()->getUser();
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
	
		$user->setUseLike("user");
		$em->persist($user);
		$em->flush();
	
		return $this->redirect($this->generateUrl('panel_child_new'));
	}	
}