<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/api")
 */
class ApiCheckController extends Controller
{
	/**
	 * @Route("/", name="api")
	 * @Template
	 */
    public function apicheckAction()
    {
	    return array();
	}

}