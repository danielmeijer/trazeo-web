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
    	
    	$form_profile = $this->createForm(new UserType(), $fos_user);
    	$form_userextend = $this->createForm(new UserExtendType(), $userextend);

	    return array(
	    		'form_profile' => $form_profile->createView(),
	    		'form_userextend' => $form_userextend->createView(),
	    		'sopinetuser' => $sopinetuser
	    		
	    );
	}
	
	/**
	 * @Route("/profile_save", name="panel_profile_save")
	 * @Template
	 */
	
	public function profilesaveAction(Request $request) {
		//ldd($request);
		//TODO: Guardar los datos recibidos del formulario
		
		$em = $this->getDoctrine()->getEntityManager();
		$fos_user = $this->get('security.context')->getToken()->getUser();
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
		$form_profile = $this->createForm(new UserType(), $fos_user);
		$form_userextend = $this->createForm(new UserExtendType(), $userextend);
		 
		$form_profile->bind($request);
		$form_userextend->bind($request);
		
		$registration = $form_profile->getData();
		$data_userextend = $form_userextend->getData();
		
		//Encriptar la password
		$factory = $this->get('security.encoder_factory');
		$encoder = $factory->getEncoder($fos_user);
		$password = $encoder->encodePassword($registration->getPassword(), $fos_user->getSalt());
		$registration->setPassword($password);
		
		$em->persist($registration);
		$em->persist($data_userextend);
		$em->flush();
		
		return $this->redirect($this->generateUrl('panel_profile'));
	
	}
	
}




