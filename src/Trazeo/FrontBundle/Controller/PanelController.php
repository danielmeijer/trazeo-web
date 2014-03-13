<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/panel")
 */
class PanelController extends Controller
{
	/**
	 * @Route("/", name="panel_dashboard"))
	 * @Template
	 */
    public function indexAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$fosUser = $this->container->get('security.context')->getToken()->getUser();
    	$fosUsername = $fosUser->getUsername();
    	$fosUserEmail = $fosUser->getEmail();
    	
    	$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findByNick($fosUsername);
    	
    	$queryGroups = $em->createQuery("SELECT g FROM TrazeoBaseBundle:Groups g JOIN g.userextendgroups u WHERE u.id = u.id");
    	$groups = $queryGroups->getResult();
    	
    	$queryChildren = $em->createQuery("SELECT c FROM TrazeoBaseBundle:Children c JOIN c.userextendchildren u WHERE u.id = u.id");
    	$children = $queryChildren->getResult();
    	
    	$queryRoutes = $em->createQuery("SELECT r FROM TrazeoBaseBundle:Routes r JOIN r.admin u WHERE u.id = u.id");
    	$routes = $queryRoutes->getResult();
    	
 
    	
    	
	    	return $this->render('TrazeoFrontBundle:Panel:home.html.twig',array(
	    			'user' => $user, 'groups' => $groups, 'children' => $children, 'routes' => $routes));  
	}
}




