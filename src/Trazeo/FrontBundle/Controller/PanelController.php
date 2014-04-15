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
    
/**    		$not = $this->container->get('sopinet_user_notification');
    		$el = $not->addNotification('child_in_group', "TrazeoBaseBundle:EChild,TrazeoBaseBundle:EGroup", "1,2");
    		ldd($not->parseNotification($el));
   **/ 	
    	
    	$childs = $user->getChilds();
        $groups = $user->getAdminGroups();
    	$routes = $user->getAdminRoutes();

    	$twig_variables = array(
            'user' => $user,
    		'childs' => $childs,
            'groups' => $groups,
    		'routes' => $routes
    	);
	    return $this->render('TrazeoFrontBundle:Panel:home.html.twig', $twig_variables);
	}
	
	/**
	 * @Route("/comment", name="panel_comment")
	 * @Template
	 */
	public function commentAction()
	{
		return array();
	
	}

}