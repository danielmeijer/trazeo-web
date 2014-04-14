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
		
		$container = $this->get('sopinet_notifier');
		$notification = $container->MessagesNotifier("success","Saved");
		
		return $this->redirect($this->generateUrl('panel_dashboard'));
		/*
		$em = $this->getDoctrine()->getEntityManager();
		$user = $this->get('security.context')->getToken()->getUser();
		$reUserValue = $em->getRepository("SusPasitosBaseBundle:UserValue");
		 
		foreach($request->request->all() as $key => $value) {
			$temp = explode("_",$key);
			if ($temp[0] == "setting") {
				$usersetting_id = $temp[1];
				$reUserValue->setValue($user, $usersetting_id, $value);
			}
		}
		 
		// TODO: MENSAJE
		return $this->redirect($this->generateUrl('panel_dashboard'));		
		*/
		//ldd($request);
		//die("Esto es lo que hay");
	}
}


