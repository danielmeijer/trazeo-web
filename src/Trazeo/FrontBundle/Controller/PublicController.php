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
	 * @Route("/", name="home")
	 * @Template
	 */
    public function indexAction()
    {
    	$banners = array();
    	
    	$banners[0]['url'] = "http://static.trazeo.es/banner/100/acerapeatonal.jpg";
    	$banners[0]['title'] = "Acera Peatonal";
    	$banners[0]['desc'] = "Entre todos hacemos calle.";
    	
    	$banners[1]['url'] = "http://static.trazeo.es/banner/100/elportillo.jpg";
    	$banners[1]['title'] = "El Portillo";
    	$banners[1]['desc'] = "Imprescindible, oportuno y necesario Trazeo y sus caminos escolares seguros.";

    	$banners[2]['url'] = "http://static.trazeo.es/banner/100/quaip.png";
    	$banners[2]['title'] = "QuaIP";
    	$banners[2]['desc'] = "Imprescindible, oportuno y necesario Trazeo y sus caminos escolares seguros.";

    	$banners[3]['url'] = "http://static.trazeo.es/banner/100/suspasitos.png";
    	$banners[3]['title'] = "SusPasitos";
    	$banners[3]['desc'] = "Pasito a pasito; por una ciudad limpia de coches y peligros para nuestros hijos.";    	
    	
    	/*
    	$banner[3]['url'] = "http://static.trazeo.es/banner/100/quaip.png";
    	$banner[3]['title'] = "DK Diseño kreativo";
    	$banner[3]['desc'] = "Imprescindible, oportuno y necesario Trazeo y sus caminos escolares seguros.";
    	*/
    	
    	
        return array(
        	'banners' => $banners
        	);
        //$this->render('TrazeoFrontBundle:Public:home.html.twig');
    }
    
    /**
     * @Route("/cofinanciadores", name="home_cofinanciadores"))
     * @Template
     */
    public function cofinanciadoresAction()
    {
    	return $this->render('TrazeoFrontBundle:Public:cofinanciadores.html.twig');
    }
}
