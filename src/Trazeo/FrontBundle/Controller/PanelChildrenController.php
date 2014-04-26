<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\BaseBundle\Entity\EChildInvite;
use Trazeo\BaseBundle\Form\ChildType;

/**
 * Child controller.
 *
 * @Route("/panel/child")
 */
class PanelChildrenController extends Controller
{

	/**
	 * Children tutor invite other Users to be child tutor.
	 *
	 * @Route("/invite", name="panel_child_invite")
	 * @Method("POST")
	 * @Template()
	 */
	public function inviteChildAction() {

		$em = $this->getDoctrine()->getManager();
		$um = $this->container->get('fos_user.user_manager');

		$container = $this->get('sopinet_flashMessages');
		
		$fos_user_current = $this->container->get('security.context')->getToken()->getUser();
		$user_current =$em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user_current);

		$userEmail = $_POST['userEmail'];
		$childId = $_POST['child'];
		
		$fos_user = $um->findUserByEmail($userEmail);
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
		

		if($fos_user == $fos_user_current ){
			$notification = $container->addFlashMessages("warning","Ya eres tutor de este niño");
			return $this->redirect($this->generateUrl('panel_child'));
		}
		
		$child = $em->getRepository('TrazeoBaseBundle:EChild')->find($childId);
		$childUsers = $child->getUserextendchilds();
		
		foreach($childUsers as $childUser){
			if($user == $childUser){
		
				$notification = $container->addFlashMessages("warning","El usuario al que quieres invitar ya es uno de los tutores del niño");
				return $this->redirect($this->generateUrl('panel_child'));
			}
		}

		if($fos_user != true){
			$notification = $container->addFlashMessages("warning","El correo electrónico introducido no corresponde a ningún usuario");
			return $this->redirect($this->generateUrl('panel_child'));
		}

		// Buscar si existe alguna petición con ese UserExtend y ese Group
		$requestUser = $em->getRepository('TrazeoBaseBundle:EChildInvite')->findOneByUserextend($user);
		$requestChild = $em->getRepository('TrazeoBaseBundle:EChildInvite')->findOneByChild($child);

		// Comprobar que existen
		if($requestUser && $requestChild == true){

			// Si existen, obtener el id de su registro en la base de datos
			$requestUserId = $requestUser->getId();
			$requestChildId = $requestChild->getId();
			// Comprobar que no tienen el mismo id de registro (petición duplicada)
			if($requestUserId = $requestChildId) {
				// Excepción y redirección
				$notification = $container->addFlashMessages("warning","Ya has invitado a este usuario anteriormente");
				return $this->redirect($this->generateUrl('panel_child'));

			}

		}else{
			// Si no existen los UserExtend y Group anteriormente obtenidos,
			// directamente se crea la petición

			$not = $this->container->get('sopinet_user_notification');
			$el = $not->addNotification(
					'child.invite.user',
					"TrazeoBaseBundle:EChild",
					$childId,
					$this->generateUrl('panel_child'), $fos_user
			);

			$access = new EChildInvite();
			$access->setChild($child);
			$access->setUserextend($user);
			$access->setSender($user_current);

			$em->persist($access);
			$em->flush();

			$container = $this->get('sopinet_flashMessages');
			$notification = $container->addFlashMessages("success","El usuario ha recibido tu invitación para ser tutor del niño");
			return $this->redirect($this->generateUrl('panel_child'));

		}

	}
	
	
	/**
	 * User disjoin as child tutor.
	 *
	 * @Route("/disjoin/{id}", name="panel_child_disjoin")
	 * @Method("GET")
	 * @Template()
	 */
	public function disJoinChildAction($id) {
	
		$em = $this->getDoctrine()->getManager();
	
		$fos_user = $this->container->get('security.context')->getToken()->getUser();
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
		$container = $this->get('sopinet_flashMessages');
		$child = $em->getRepository('TrazeoBaseBundle:EChild')->find($id);
	
		if (!$child) {
			$notification = $container->addFlashMessages("warning","El registro del niño ha sido eliminado");
			return $this->redirect($this->generateUrl('panel_child'));
		}
	
		$child->removeUserextendchild($user);
		$em->persist($child);
		$em->flush();
	
		return $this->redirect($this->generateUrl('panel_child'));
	}
	

    /**
     * Lists all Child entities.
     *
     * @Route("/", name="panel_child")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $fos_user = $this->container->get('security.context')->getToken()->getUser();
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        $allChildsInvite = $em->getRepository('TrazeoBaseBundle:EChildInvite')->findAll();
        
        $childs = $user->getChilds();

        return array(
            'childs' => $childs,
        	'allChildsInvite' => $allChildsInvite,
        	'user' => $user,
        );
    }
    
    
    /**
     * User accept to be children tutor.
     *
     * @Route("/inviteaccept/{id}/{child}/{sender}", name="panel_child_invite_accept")
     * @Method("GET")
     * @Template()
     */
    public function acceptInviteGroupAction($id, $child,$sender) {
        	
    	$em = $this->getDoctrine()->getManager();
  
    	$fos_user = $this->container->get('security.context')->getToken()->getUser();
    	$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
    	
    	$childToJoin = $em->getRepository('TrazeoBaseBundle:EChild')->find($child);
    	$container = $this->get('sopinet_flashMessages');
    
    	if (!$childToJoin) {
    		$notification = $container->addFlashMessages("success","No puedes unirte al grupo porque ha sido eliminado");
    		return $this->redirect($this->generateUrl('panel_child'));
    	}
    
    	$userRequest = $em->getRepository('TrazeoBaseBundle:EChildInvite')->findOneByUserextend($id);
    	$userextend = $userRequest->getUserextend();
    	
    	$userSender = $em->getRepository('TrazeoBaseBundle:Userextend')->findOneById($sender);
    	$fos_userSender = $userSender->getUser();

    	$childToJoin->addUserextendchild($userextend);
    	$em->persist($childToJoin);
    
    	$em->remove($userRequest);
    	$em->flush();
    
    	$not = $this->container->get('sopinet_user_notification');
    	$el = $not->addNotification(
    			'child.invite.accept',
    			"TrazeoBaseBundle:Userextend,TrazeoBaseBundle:EChild",
    			$id . "," . $child,
    			$this->generateUrl('panel_child'), $fos_userSender
    	);
    
    
    	$notification = $container->addFlashMessages("success","Has aceptado la invitación para ser tutor");
    	return $this->redirect($this->generateUrl('panel_child'));
    }
    
    
    /**
     * User refuse an invite to be child tutor.
     *
     * @Route("/invitedeny/{id}/{child}/{sender}", name="panel_child_invite_deny")
     * @Method("GET")
     * @Template()
     */
    
    public function denyInviteChildAction($id,$child,$sender) {
    
    	$em = $this->getDoctrine()->getManager();
    
    	$userRequest = $em->getRepository('TrazeoBaseBundle:EChildInvite')->findOneByUserextend($id);
    
    	$em->remove($userRequest);
    	$em->flush();
    
    	$userSender = $em->getRepository('TrazeoBaseBundle:Userextend')->findOneById($sender);
    	$fos_userSender = $userSender->getUser();
    	$not = $this->container->get('sopinet_user_notification');
    	$el = $not->addNotification(
    			'child.invite.deny',
    			"TrazeoBaseBundle:Userextend,TrazeoBaseBundle:EChild",
    			$id . "," . $child,
    			$this->generateUrl('panel_child'), $fos_userSender
    	);
    
    	$container = $this->get('sopinet_flashMessages');
    	$notification = $container->addFlashMessages("success","Has rechazado la invitación");
    
    	return $this->redirect($this->generateUrl('panel_child'));
    
    }
    
    
    /**
     * Creates a new Childs entity.
     *
     * @Route("/", name="panel_child_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $child = new EChild();
        $form = $this->createCreateForm($child);
        $form->handleRequest($request);

        if ($form->isValid()) 
            $em = $this->getDoctrine()->getManager();
            
            $fos_user = $this->container->get('security.context')->getToken()->getUser();
            $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
            
            //$user->addChild($child);
            $child->addUserextendchild($user);            
            $em->persist($child);
            $em->flush();

            return $this->redirect($this->generateUrl('panel_child'));
    }

    /**
     * Creates a form to create a Child entity.
     *
     * @param Child $child The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EChild $child)
    {
        $form = $this->createForm(new ChildType(), $child, array(
            'action' => $this->generateUrl('panel_child_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Child entity.
     *
     * @Route("/new", name="panel_child_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $child = new EChild();
        $form   = $this->createCreateForm($child);

        return array(
            'child' => $child,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Child entity.
     *
     * @Route("/{id}/show", name="panel_child_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $child = $em->getRepository('TrazeoBaseBundle:EChild')->find($id);

        if (!$child) {
            throw $this->createNotFoundException('Unable to find Child entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'child'    => $child,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Child entity.
     *
     * @Route("/{id}/edit", name="panel_child_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction(Echild $child)
    {
        $em = $this->getDoctrine()->getManager();

        $fos_user = $this->container->get('security.context')->getToken()->getUser();
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        
        //Comprobamos que el usuario logueado está dentro del array de tutores del niño
        $userextends = $child->getUserextendchilds()->toArray();
        $users = array();
        foreach($userextends as $userextend){
        	if($user == $userextend){
        		$users[] = $userextend;
        	}
        }
        $container = $this->get('sopinet_flashMessages');
        if(count($users) < 1){
        	
        	$notification = $container->addFlashMessages("error","No tienes permisos para editar la información de este niño");
        	return $this->redirect($this->generateUrl('panel_child'));
        }

        if (!$child) {
        	$notification = $container->addFlashMessages("warning","El registro indicado no existe");
        	return $this->redirect($this->generateUrl('panel_child'));
        }

        $editForm = $this->createEditForm($child);
        $deleteForm = $this->createDeleteForm($child);

        return array(
            'child'      => $child,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Child entity.
    *
    * @param Child $child The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EChild $child)
    {
        $form = $this->createForm(new ChildType(), $child, array(
            'action' => $this->generateUrl('panel_child_update', array('id' => $child->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Child entity.
     *
     * @Route("/{id}", name="panel_child_update")
     * @Method("PUT")
     * @Template("TrazeoBaseBundle:EChild:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $child = $em->getRepository('TrazeoBaseBundle:EChild')->find($id);

        if (!$child) {
            throw $this->createNotFoundException('Unable to find Child entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($child);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('panel_child_edit', array('id' => $id)));
        }

        return array(
            'child'    => $child,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Child entity.
     *
     * @Route("/{id}/delete", name="panel_child_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $fos_user = $this->container->get('security.context')->getToken()->getUser();
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        $child = $em->getRepository('TrazeoBaseBundle:EChild')->find($id);
        
        $container = $this->get('sopinet_flashMessages');
        if (!$child) {
        	$notification = $container->addFlashMessages("warning","El registro del niño que intentas eliminar no existe");
        	return $this->redirect($this->generateUrl('panel_child'));
        }
        
        $userextends = $child->getUserextendchilds()->toArray();
        $users = array();
        foreach($userextends as $userextend){
        	if($user == $userextend){
        		$users[] = $userextend;
        	}
        }
        
		if($users){

			$em->remove($child);
			$em->flush();
			$notification = $container->addFlashMessages("success","El registro de niño ha sido eliminado");
			return $this->redirect($this->generateUrl('panel_child'));

		}else {
			$notification = $container->addFlashMessages("error","Sólo un tutor puede eliminar un registro niño");
			return $this->redirect($this->generateUrl('panel_child'));	
		}
    }

    /**
     * Creates a form to delete a Child entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('panel_child_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}