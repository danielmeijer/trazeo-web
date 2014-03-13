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
    	$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fosUser);
    
    	$groups = $user->getGroups();
    	$children = $user->getChildren();
    	$routes = $user->getAdminRoutes();

	    	return $this->render('TrazeoFrontBundle:Panel:home.html.twig',array(
	    			'user' => $user, 'groups' => $groups, 'children' => $children, 'routes' => $routes));  
	}
}




