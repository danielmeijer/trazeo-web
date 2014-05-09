<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Trazeo\BaseBundle\Entity\EGroupInvite;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

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
    	return array();
    }
    
    /**
     * @Route("/invite/{email}/{token}/{id}", name="home_invite_user")
     * @Template
     */
    public function inviteAction($email, $token, $id) {
    	// Comprobar TOKEN
    	$em = $this->getDoctrine()->getManager();
    	$reGAI = $em->getRepository('TrazeoBaseBundle:EGroupAnonInvite');
    	$inviterow = $reGAI->findOneById($id);
    	
    	if ($inviterow->getToken() == $token
    			&& $inviterow->getEmail() == $email) {
    		// Mostramos la pantalla
    		return array(
    				'id' => $id,
    				'token' => $token,
    				'email' => $email
    		);
    	} else {
    		die("Error");
    	}
    }
    
    /**
     * @Route("/execInvite", name="home_execInvite_user")
     * @param Request $request
     */
    public function execInviteAction(Request $request) {
    	$em = $this->getDoctrine()->getManager();
    	$pass1 = $request->get('_password1');
    	$pass2 = $request->get('_password2');
    	$id = $request->get('id');
    	$token = $request->get('token');    	
    	
    	if ($pass1 != $pass2) {
    		die("Pass no es el mismo");
    		// TODO: Comprobar pass no es el mismo
    	}
    	
    	$reGAI = $em->getRepository('TrazeoBaseBundle:EGroupAnonInvite');
    	$inviterow = $reGAI->findOneById($id);
    	
    	if ($inviterow->getToken() == $token) {
    		// Datos
    		$groupId = $inviterow->getGroup()->getId();
    		
    		//Creamos el usuario
    		$user = new \Application\Sonata\UserBundle\Entity\User;
    		$user->setEmail($inviterow->getEmail());
    		$user->setPlainPassword($pass1);
    		$user->setUsername($inviterow->getEmail());
    		$user->setEnabled(1);
    		
    		$em->persist($user);
    		$em->flush();

    		// Logueamos Usuario
   			$token = new UsernamePasswordToken($user, null, "your_firewall_name", $user->getRoles());
   			$this->get("security.context")->setToken($token); //now the user is logged in
    			 
   			// Lanzamos evento de Login
   			$request = $this->get("request");
   			$event = new InteractiveLoginEvent($request, $token);
   			$this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

    		// Creamos la invitación
    		$not = $this->container->get('sopinet_user_notification');
    		$el = $not->addNotification(
    				'group.invite.user',
    				"TrazeoBaseBundle:EGroup",
    				$groupId,
    				$this->generateUrl('panel_group'), $user
    		);
    			
    		$access = new EGroupInvite();
    		$access->setGroup($inviterow->getGroup());
    		$access->setUserextend($user->getUserExtend());
    		
    		$em->persist($access);
    		$em->flush();
    		
    		$container = $this->get('sopinet_flashMessages');
    		$notification = $container->addFlashMessages("success","¡Bienvenido a Trazeo!");
    		//return $this->redirect($this->generateUrl('panel_group_timeline',array('id'=>$groupId)));
    		return $this->redirect($this->generateUrl('panel_dashboard'));
    		// TODO: Crear un Usuario y redirigir 	
    	} else {
			die("Error");
    	}
    }
}
