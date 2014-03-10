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
        return $this->render('TrazeoFrontBundle:Panel:home.html.twig');
    }
}
