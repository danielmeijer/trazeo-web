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
 * @Route("/panel/settings")
 */
class PanelSettingsController extends Controller
{
	/**
	 * Show user preferences edit screen.
	 *
	 * @Route("/edit", name="panel_user_settings")
	 * @Method("GET")
	 * @Template()
	 */
	public function editAction()
	{
		$sup = $this->container->get('sopinet_user_preferences');
		$settings = $sup->getAllSettings();
		return array('settings' => $settings);
	}

	
	/**
	 * Show user preferences edit screen.
	 *
	 * @Route("/save", name="panel_user_settings_save")
	 * @Template()
	 */	
	public function saveAction(Request $request)
	{
		$sup = $this->container->get('sopinet_user_preferences');
		$sup->setAllSettings($request);
		
		$container = $this->get('sopinet_flashMessages');
		// TODO: Traducir mensaje de Guardadas Preferencias de Usuario
		$notification = $container->addFlashMessages("success","Guardadas preferencias de usuario");
		
		return $this->redirect($this->generateUrl('panel_user_settings'));
	}
}


