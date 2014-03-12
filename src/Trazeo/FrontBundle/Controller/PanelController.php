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
    	
    	// Acceso al usuario logueado
    	
    	$em = $this->getDoctrine()->getManager();
    	$um = $this->container->get('fos_user.user_manager');
    	
    	$user = $this->container->get('security.context')->getToken()->getUser();
    	$username =  $user->getUsername();
    	
    	$entity = $em->getRepository('TrazeoBaseBundle:UserExtend')->findByNick($username);
    
    	
  
    	if (!$entity) {
    		throw $this->createNotFoundException('Error.');
	    	}
	    	
	    	return $this->render('TrazeoFrontBundle:Panel:home.html.twig',array(
	    			'entity' => $entity
	    	)
    	);    
	}
}