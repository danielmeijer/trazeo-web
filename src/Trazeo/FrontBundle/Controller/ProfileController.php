<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Application\Sonata\UserBundle\Entity\User;
use Trazeo\BaseBundle\Entity\UserExtend;
use Trazeo\BaseBundle\Form\UserType;
use Trazeo\BaseBundle\Form\UserExtendType;

/**
 * @Route("/panel")
 */
class ProfileController extends Controller
{
	/**
	 * @Route("/profile", name="panel_profile"))
	 * @Template()
	 */
    public function profileAction()
    {
    	$em = $this->getDoctrine()->getManager();
    	$fos_user = $this->container->get('security.context')->getToken()->getUser();	
    	$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
    	$sopinetuser = $em->getRepository('\Sopinet\UserBundle\Entity\SopinetUserExtend')->findOneByUser($fos_user->getId());
    	
    	$spainCode = $em->getRepository('JJs\Bundle\GeonamesBundle\Entity\Country')->findOneByCode("ES");
    	$spainCodeId = $spainCode->getId();
    	 
    	$form_profile = $this->createForm(new UserType(), $fos_user);
    	$form_userextend = $this->createForm(new UserExtendType(), $userextend, array(
            'action' => $this->generateUrl('panel_userextend_create'),
            'method' => 'POST',
        	'attr' => array(
        				'Userextend.help.nick' => $this->get('translator')->trans('Userextend.help.nick'),
        				'Userextend.help.mobile' => $this->get('translator')->trans('Userextend.help.mobile'),
        				'default' => $spainCodeId
        		)
        	));
    	//TODO: Error cuando las contraseñas no coinciden
    	
	    return array(
	    		'form_profile' => $form_profile->createView(),
	    		'form_userextend' => $form_userextend->createView(),
	    		'sopinetuser' => $sopinetuser
	    		
	    );
	}
	
	
	/**
	 * @Route("/profile_notifications", name="panel_profile_notifications"))
	 * @Template()
	 */
	public function profileNotificationsAction()
	{
		$sup = $this->container->get('sopinet_user_notification');
		$notifications = $sup->getAllNotifications();

		return array('notifications' => $notifications);
	}
	
	/**
	 * @Route("/profile_save", name="panel_profile_save")
	 * @Template
	 */
	
	public function profilesaveAction(Request $request) {
		//TODO: Guardar los datos recibidos del formulario
		
		$em = $this->getDoctrine()->getEntityManager();
		$fos_user = $this->container->get('security.context')->getToken()->getUser();
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
		
		$spainCode = $em->getRepository('JJs\Bundle\GeonamesBundle\Entity\Country')->findOneByCode("ES");
		$spainCodeId = $spainCode->getId();
		
		$form_profile = $this->createForm(new UserType(), $fos_user);
    	$form_userextend = $this->createForm(new UserExtendType(), $userextend, array(
            'action' => $this->generateUrl('panel_userextend_create'),
            'method' => 'POST',
        	'attr' => array(
        				'Userextend.help.nick' => $this->get('translator')->trans('Userextend.help.nick'),
        				'Userextend.help.mobile' => $this->get('translator')->trans('Userextend.help.mobile'),
        				'default' => $spainCodeId,
        		)
        	));
    	//Guarda la pass en una variable antes de que se pise en el bind
    	$password = $fos_user->getPassword();
		
    	$form_profile->bind($request);
		$form_userextend->bind($request);
		
		$registration = $form_profile->getData();
		$data_userextend = $form_userextend->getData();
		
		if($registration->getPassword() != null){
			//Encriptar la password
			$factory = $this->get('security.encoder_factory');
			$encoder = $factory->getEncoder($fos_user);
			$password = $encoder->encodePassword($registration->getPassword(), $fos_user->getSalt());		
		}
		
		$registration->setPassword($password);
		
		$city = $request->get('city');
		$helper = $this->get('trazeo_base_helper');
		$city_entity = $helper->getCities($city, 10, true);
		if (count($city_entity) > 0) {
			$data_userextend->setCity($city_entity[0]);
		}
		
		$em->persist($registration);
		$em->persist($data_userextend);		
		
		$em->flush();
			
		$container = $this->get('sopinet_flashMessages');
		// TODO: Traducir mensaje de Guardadas Preferencias de Usuario
		$notification = $container->addFlashMessages("success","Guardado perfil de usuario");
		
		return $this->redirect($this->generateUrl('panel_dashboard'));
	}
	
	/**
	 * Deletes a UserExtend entity.
	 *
	 * @Route("/profile_delete", name="panel_profile_delete")
	 */
	public function deleteAction(Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		$um = $this->container->get('fos_user.user_manager');
		 
		$fos_user = $this->container->get('security.context')->getToken()->getUser();
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
	
		$em->remove($user);
		$em->remove($fos_user);
		$em->flush();
	
		return $this->redirect($this->generateUrl('home'));
	}	
}