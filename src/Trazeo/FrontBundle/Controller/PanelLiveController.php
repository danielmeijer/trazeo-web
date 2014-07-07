<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Trazeo\BaseBundle\Entity\UserExtend;
use Trazeo\BaseBundle\Form\UserExtendType;

/**
 * UserExtend controller.
 *
 * @Route("/panel/live")
 */
class PanelLiveController extends Controller
{
	/**
	 * Show user live preferences edit screen.
	 *
	 * @Route("/edit", name="panel_user_live")
	 * @Method("GET")
	 * @Template()
	 */
	public function editAction()
	{
		$sup = $this->container->get('sopinet_user_notifications');
		$settings = $sup->getAllLiveSettings();
		return array('settings' => $settings);
	}

	
	/**
	 * Save user live preferences from edit screen.
	 *
	 * @Route("/save", name="panel_user_live_save")
	 * @Template()
	 */	
	public function saveAction(Request $request)
	{
		$sup = $this->container->get('sopinet_user_notifications');
		$sup->setAllLiveSettings($request);
		ldd($sup);
		
		$container = $this->get('sopinet_flashMessages');
		// TODO: Traducir mensaje de Guardadas Preferencias de Usuario
		$notification = $container->addFlashMessages("success","Guardadas preferencias de usuario");
		
		return $this->redirect($this->generateUrl('panel_user_live'));
	}
}