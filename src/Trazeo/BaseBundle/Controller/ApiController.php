<?php

namespace Trazeo\BaseBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\RouteRedirectView;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller {
	
	/**
	 * @Get("/api/groups")
	 */
	public function getGroupsAction(Request $request) {
		
		$em = $this->get('doctrine.orm.entity_manager');
		$fos_user = $this->container->get('security.context')->getToken()->getUser();
		//61ZcV10HTyBO4pcDLaMza14ljSVip46I
		$hola1 = $this->get('apikeyauthenticator')->createToken($request, $fos_user->getSalt());
		//ldd($hola1);
		//$username = $fos_user->getUsername();
		//$hola = $this->get('webserviceuserprovider')->loadUserByUsername($fos_user);
		ldd($hola);
		
		$em = $this->get('doctrine.orm.entity_manager');
		$fos_user = $this->container->get('security.context')->getToken()->getUser();
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
		
		//$reGroup = $em->getRepository("TrazeoBaseBundle:EGroup");
		//$groups = $reGroup->findByUserextendgroups($user);
		//ldd($user->getGroups()->toArray());
		$groups = $user->getGroups();
		
		$view = View::create ()
		->setStatusCode ( 200 )
		->setData ( $groups );
		
		return $this->get ( 'fos_rest.view_handler' )->handle ( $view );
	}
}
