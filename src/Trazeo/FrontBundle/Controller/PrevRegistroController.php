<?php 

namespace Trazeo\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
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
	public function prevRegistroAction(Request $request)
	{
		$userManager = $this->container->get('fos_user.user_manager');
		$em = $this->getDoctrine()->getManager();
        /** @var Translator $translator */
        $translator = $this->get('translator');
		$email= $this->get('request')->get('email');
		$search = $userManager->findUserByEmail($email);
		
		if($search == true){
						
		   	$container = $this->get('sopinet_flashMessages');
		   	$notification = $container->addFlashMessages("error", $translator->trans('flash_messages.user_already_exists'));

			return $this->redirect($this->generateUrl('home'));
			
		}else{
			
			$user = $userManager->createUser();
			$user->setUsername($email);
			$user->setEmail($email);
			$user->setPassword($email);
			$user->setTutorial(0);
			
			// Usuario activado por defecto
			$user->setEnabled(false);
			
			// Asignación de permisos
			$user->addRole('ROLE_USER');
			$userManager->updateUser($user);
			
			$em->persist($user);
			$em->flush();
				
			$container = $this->get('sopinet_flashMessages');
			$notification = $container->addFlashMessages("success", $translator->trans('flash_messages.add_success'));

			return $this->redirect($this->generateUrl('home'));
		}
	}
}