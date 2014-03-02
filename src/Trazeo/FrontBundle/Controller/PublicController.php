<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/")
 */
class PublicController extends Controller
{
	/**
	 * @Route("/", name="home"))
	 * @Template
	 */
    public function indexAction()
    {
        return $this->render('TrazeoFrontBundle:Public:home.html.twig');
    }
    
    public function getTokenAction()
    {
    	return new Response($this->container->get('form.csrf_provider')
    			->generateCsrfToken('authenticate'));
    }
}
