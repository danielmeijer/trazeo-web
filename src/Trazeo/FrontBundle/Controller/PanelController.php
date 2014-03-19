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
    	$fos_user = $this->container->get('security.context')->getToken()->getUser();	
    	$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
    
    	$children = $user->getChildren();
        $groups = $user->getAdminGroup();
    	$routes = $user->getAdminRoutes();

    	$twig_variables = array(
            'user' => $user,
    		'children' => $children,
            'groups' => $groups,
    		'routes' => $routes
    	);
    	
	    return $this->render('TrazeoFrontBundle:Panel:home.html.twig', $twig_variables);
	}
	
}




