<?php 

namespace Trazeo\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Trazeo\BaseBundle\Entity\UserExtend;

/**
 * @Route("")
 */
class PrevRegistroController extends Controller
{

	/**
	 * @Route("/prev_registro", name="prev_registro"))
	 * @Template
	 */
	public function prevRegistroAction()
	{
		$userManager = $this->container->get('fos_user.user_manager');
		$em = $this->getDoctrine()->getManager();
		
			$user = $userManager->createUser();
			$email= $this->get('request')->request->get('email');
			$user->setUsername($email);
			$user->setEmail($email);
			$user->setPassword($email);
		
			// Usuario activado por defecto
			$user->setEnabled(true);
		
			// Asignación de permisos
			$user->addRole('ROLE_USER');
			$userManager->updateUser($user);
		
			$em->persist($user);
			$em->flush();
		
			return $this->redirect($this->generateUrl('home'));
			
	}
}