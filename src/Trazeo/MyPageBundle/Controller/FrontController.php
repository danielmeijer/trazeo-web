<?php

namespace Trazeo\MyPageBundle\Controller;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\Controller\FOSRestController;
use Trazeo\MyPageBundle\Classes\Module\TrazeoGroups;

/**
* @Route("/landingPage")
*/
class FrontController extends Controller
{
/**
* @Route("/{subdomain}", name="landingPage")
* @Template()
*/
   public function landingPageAction($subdomain)
   {
	   	$em = $this->getDoctrine()->getEntityManager();

       $repositoryPage = $em->getRepository("TrazeoMyPageBundle:Page");
       $page = $repositoryPage->findOneBySubdomain($subdomain);
       
	   	return array(
            'container' => $this,
            'page' => $page
	   	);
	}
}