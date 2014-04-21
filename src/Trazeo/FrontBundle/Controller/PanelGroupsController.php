<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Trazeo\BaseBundle\Entity\EGroup;
use Trazeo\BaseBundle\Entity\EGroupAccess;
use Trazeo\BaseBundle\Entity\EGroupInvite;
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\BaseBundle\Form\GroupType;
use Trazeo\BaseBundle\Controller\GroupsController;

/**
 * Groups controller.
 *
 * @Route("/panel/group")
 */
class PanelGroupsController extends Controller
{
	/**
	 * User join Child to Group.
	 *
	 * @Route("/{group}/joinchild/{child}", name="panel_group_joinChild")
	 * @Method("GET")
	 */
	public function joinChildAction(EGroup $group, EChild $child) {
	
		$em = $this->getDoctrine()->getManager();
	
		$fos_user = $this->container->get('security.context')->getToken()->getUser();
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
	
		$group->addChild($child);
		//ldd($group->getChilds()->toArray());
		$em->persist($group);
		$em->flush();
	
		return $this->redirect($this->generateUrl('panel_group_timeline', array('id' => $group->getId())));
	}
	
	/**
	 * User join Group.
	 *
	 * @Route("/join/{id}", name="panel_group_join")
	 * @Method("GET")
	 * @Template()
	 */
	public function joinGroupAction($id) {
		
		$em = $this->getDoctrine()->getManager();
		
		$fos_user = $this->container->get('security.context')->getToken()->getUser();
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
		
		$group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($id);
		
		if (!$group) {
			throw $this->createNotFoundException('Unable to find Group entity.');
		}
		
		$group->addUserextendgroup($user);
		$em->persist($group);
		$em->flush();
		$container = $this->get('sopinet_flashMessages');
		$notification = $container->addFlashMessages("success","Has sido añadido al grupo correctamente");
		return $this->redirect($this->generateUrl('panel_group'));
	}	
	
	/**
	 * User disjoin Group.
	 *
	 * @Route("/disjoin/{id}", name="panel_group_disjoin")
	 * @Method("GET")
	 * @Template()
	 */
	public function disJoinGroupAction($id) {
	
		$em = $this->getDoctrine()->getManager();
	
		$fos_user = $this->container->get('security.context')->getToken()->getUser();
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
	
		$group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($id);
	
		if (!$group) {
			throw $this->createNotFoundException('Unable to find Group entity.');
		}
	
		$group->removeUserextendgroup($user);
		$em->persist($group);
		$em->flush();
	
		return $this->redirect($this->generateUrl('panel_group'));
	}
	
	/**
	 * Request to admin Group.
	 *
	 * @Route("/requestjoin/{id}", name="panel_group_requestJoin")
	 * @Method("GET")
	 * @Template()
	 */
	public function requestJoinGroupAction($id) {
	
		$em = $this->getDoctrine()->getManager();
		// FOSUser y UserExtend correspondiente logueado
		$fos_user = $this->container->get('security.context')->getToken()->getUser();
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
		// Obtener grupo al que se quiere unir a través del param $id
		$group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($id);
		// Buscar si existe alguna petición con ese UserExtend y ese Group
		$requestUser = $em->getRepository('TrazeoBaseBundle:EGroupAccess')->findOneByUserextend($user);
		$requestGroup = $em->getRepository('TrazeoBaseBundle:EGroupAccess')->findOneByGroup($group);
		
		// Comprobar que existen
		if($requestUser && $requestGroup == true){
			
			// Si existen, obtener el id de su registro en la base de datos
			$requestUserId = $requestUser->getId();
			$requestGroupId = $requestGroup->getId();
			// Comprobar que no tienen el mismo id de registro (petición duplicada)
			if($requestUserId = $requestGroupId) {
				// Excepción y redirección
				$container = $this->get('sopinet_flashMessages');
				$notification = $container->addFlashMessages("warning","Ya has solicitado el acceso a este grupo anteriormente");
				return $this->redirect($this->generateUrl('panel_group'));
					
			}
			
		}else{
		// Si no existen los UserExtend y Group anteriormente obtenidos,
		// directamente se crea la petición
			$access = new EGroupAccess();
			$access->setGroup($group);
			$access->setUserextend($user);
			
			$em->persist($access);
			$em->flush();
			
			$container = $this->get('sopinet_flashMessages');
			$notification = $container->addFlashMessages("success","Tu solicitud para unirte ha sido enviada al adminsitrador del grupo");
			return $this->redirect($this->generateUrl('panel_group'));	
			
		}

	}
		
	
	/**
	 * User adminGroup let an User to join with the request Group.
	 *
	 * @Route("/letjoin/{id}/{group}", name="panel_group_let_join")
	 * @Method("GET")
	 * @Template()
	 */
	public function letJoinGroupAction($id, $group) {
	
		$em = $this->getDoctrine()->getManager();
	
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->find($id);
		$groupToJoin = $em->getRepository('TrazeoBaseBundle:EGroup')->find($group);
	
		if (!$groupToJoin) {
			throw $this->createNotFoundException('Unable to find Group entity.');
		}
	
		$groupToJoin->addUserextendgroup($user);
		$em->persist($groupToJoin);
		$em->flush();
		
		$userRequest = $em->getRepository('TrazeoBaseBundle:EGroupAccess')->findOneByUserextend($id);
		
		$em->remove($userRequest);
		$em->flush();
		//$allRequest= $em->remove($userRequest);
		
		$container = $this->get('sopinet_flashMessages');
		$notification = $container->addFlashMessages("success","El usuario ha sido añadido al grupo");
	
		return $this->redirect($this->generateUrl('panel_group'));
	}
	
	/**
	 * User adminGroup let an User to join with the request Group.
	 *
	 * @Route("/denyjoin/{id}", name="panel_group_deny_join")
	 * @Method("GET")
	 * @Template()
	 */
	
	public function denyJoinGroupAction($id) {
		
		$em = $this->getDoctrine()->getManager();
		
		$userRequest = $em->getRepository('TrazeoBaseBundle:EGroupAccess')->findOneByUserextend($id);
		
		$em->remove($userRequest);
		$em->flush();
		
		$container = $this->get('sopinet_flashMessages');
		$notification = $container->addFlashMessages("success","La petición del usuario para unirse al grupo ha sido denegada");
		
		return $this->redirect($this->generateUrl('panel_group'));
		
	}
	
	
	/**
	 * GroupAdmin invite an User to join a Group.
	 *
	 * @Route("/invite", name="panel_group_invite")
	 * @Method("POST")
	 * @Template()
	 */
	public function inviteGroupAction() {
	
		$em = $this->getDoctrine()->getManager();
		$um = $this->container->get('fos_user.user_manager');
		
		
		$fos_user_current = $this->container->get('security.context')->getToken()->getUser();
		$user_current =$um->findUserByEmail($fos_user_current);
	
		$userEmail = $_POST['userEmail'];	
		$groupId = $_POST['group'];
				
		$fos_user = $um->findUserByEmail($userEmail);
		if($fos_user != true){
			$container = $this->get('sopinet_flashMessages');
			$notification = $container->addFlashMessages("warning","El correo electrónico introducido no corresponde a ningún usuario");
			return $this->redirect($this->generateUrl('panel_group'));
		}
		
		if($fos_user == $fos_user_current ){
			$container = $this->get('sopinet_flashMessages');
			$notification = $container->addFlashMessages("warning","No necesitas invitación para unirte a un grupo del que eres administrador");
			return $this->redirect($this->generateUrl('panel_group'));
		}
		
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
		
		// Obtener grupo al que se va a unir a través del param $id
		$group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($groupId);
		// Buscar si existe alguna petición con ese UserExtend y ese Group
		$requestUser = $em->getRepository('TrazeoBaseBundle:EGroupInvite')->findOneByUserextend($user);
		$requestGroup = $em->getRepository('TrazeoBaseBundle:EGroupInvite')->findOneByGroup($group);
	
		// Comprobar que existen
		if($requestUser && $requestGroup == true){
				
			// Si existen, obtener el id de su registro en la base de datos
			$requestUserId = $requestUser->getId();
			$requestGroupId = $requestGroup->getId();
			// Comprobar que no tienen el mismo id de registro (petición duplicada)
			if($requestUserId = $requestGroupId) {
				// Excepción y redirección
				$container = $this->get('sopinet_flashMessages');
				$notification = $container->addFlashMessages("warning","Ya has invitado a este usuario anteriormente");
				return $this->redirect($this->generateUrl('panel_group'));
					
			}
				
		}else{
			// Si no existen los UserExtend y Group anteriormente obtenidos,
			// directamente se crea la petición
			$access = new EGroupInvite();
			$access->setGroup($group);
			$access->setUserextend($user);
				
			$em->persist($access);
			$em->flush();
				
			$container = $this->get('sopinet_flashMessages');
			$notification = $container->addFlashMessages("success","El usuario ha recibido tu invitación para unirse al grupo");
			return $this->redirect($this->generateUrl('panel_group'));
				
		}
	
	}
	
	
	/**
	 * User accept to join with a hidden group.
	 *
	 * @Route("/acceptinvite/{id}/{group}", name="panel_group_accept_invite")
	 * @Method("GET")
	 * @Template()
	 */
	public function acceptInviteGroupAction($id, $group) {
	
		$em = $this->getDoctrine()->getManager();
	
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->find($id);
		$groupToJoin = $em->getRepository('TrazeoBaseBundle:EGroup')->find($group);
	
		if (!$groupToJoin) {
			throw $this->createNotFoundException('Unable to find Group entity.');
		}
	
		$groupToJoin->addUserextendgroup($user);
		$em->persist($groupToJoin);
		$em->flush();

		$userRequest = $em->getRepository('TrazeoBaseBundle:EGroupInvite')->findOneByUserextend($id);
	
		$em->remove($userRequest);
		$em->flush();
		//$allRequest= $em->remove($userRequest);
	
		$container = $this->get('sopinet_flashMessages');
		$notification = $container->addFlashMessages("success","Te has unido correctamente al grupo oculto");
	
		return $this->redirect($this->generateUrl('panel_group'));
	}
	
	
	/**
	 * User adminGroup let an User to join with the request Group.
	 *
	 * @Route("/acceptdeny/{id}", name="panel_group_deny_invite")
	 * @Method("GET")
	 * @Template()
	 */
	
	public function denyInviteGroupAction($id) {
	
		$em = $this->getDoctrine()->getManager();
	
		$userRequest = $em->getRepository('TrazeoBaseBundle:EGroupInvite')->findOneByUserextend($id);
	
		$em->remove($userRequest);
		$em->flush();
	
		$container = $this->get('sopinet_flashMessages');
		$notification = $container->addFlashMessages("success","Has rechazado la invitación");
	
		return $this->redirect($this->generateUrl('panel_group'));
	
	}
	
    /**
     * Lists all Groups entities.
     *
     * @Route("/", name="panel_group")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $fos_user = $this->container->get('security.context')->getToken()->getUser();
        
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        // Grupos a los que pertenece el usuario
        $userGroups = $user->getGroups();
        $userId = $user->getId();
        
        // Grupos de los cuales el usuario es administrador
        $userAdmin = $em->getRepository('TrazeoBaseBundle:EGroup')->findByAdmin($userId);
        // Listado de todas las peticiones de acceso a un grupo por parte de otros usuarios
        $allGroupsAccess = $em->getRepository('TrazeoBaseBundle:EGroupAccess')->findAll();
        $allGroupsInvite = $em->getRepository('TrazeoBaseBundle:EGroupInvite')->findAll();
        
        // Se cogen todos los grupos y se "restan" los cuales el usuario forma parte
        $allGroups = $em->getRepository('TrazeoBaseBundle:EGroup')->findAll();
        $groups = array_diff($allGroups,$userGroups->toArray());
        
        return array(
            'groups' => $groups,
        	'userGroups' => $userGroups,
        	'userAdmin' => $userAdmin,
        	'allGroupsAccess' => $allGroupsAccess,
        	'allGroupsInvite' => $allGroupsInvite,
        	'user' => $user
        );
    }
    /**
     * Creates a new Group entity.
     *
     * @Route("/", name="panel_group_create")
     * @Method("POST")
     * @Template("TrazeoBaseBundle:Groups:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $group = new EGroup();
        $form = $this->createCreateForm($group);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $fos_user = $this->container->get('security.context')->getToken()->getUser();
            $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
            
            $group->setAdmin($user);
            $group->addUserextendgroup($user);            
            
            $em->persist($group);
            $em->flush();

            return $this->redirect($this->generateUrl('panel_group'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Group entity.
     *
     * @param Group $group
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EGroup $group)
    {
        $form = $this->createForm(new GroupType(), $group, array(
            'action' => $this->generateUrl('panel_group_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Groups entity.
     *
     * @Route("/new", name="panel_group_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $group = new EGroup();
        $form   = $this->createCreateForm($group);

        return array(
            'entity' => $group,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Groups entity.
     *
     * @Route("/{id}", name="panel_groups_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrazeoBaseBundle:Groups')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Groups entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Group entity.
     *
     * @Route("/{id}/edit", name="panel_group_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($id);

        if (!$group) {
            throw $this->createNotFoundException('Unable to find Group entity.');
        }

        $editForm = $this->createEditForm($group);
        //$deleteForm = $this->createDeleteForm($id);

        return array(
            'group'      => $group,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Group entity.
    *
    * @param EGroup $group
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EGroup $group)
    {
        $form = $this->createForm(new GroupType(), $group, array(
            'action' => $this->generateUrl('panel_group_update', array('id' => $group->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    /**
     * @Route("/{id}/timeline", name="panel_group_timeline")
     * @Template()
     */
    public function timelineAction(Egroup $group)
    {
    	$em = $this->getDoctrine()->getManager();
    	$fos_user = $this->container->get('security.context')->getToken()->getUser();
    	$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
    	
    	//Listado de niños que están en el grupo y pertenecen al usuario logueado 
    	$userchilds = $user->getChilds()->toArray();
    	$groupchilds = $group->getChilds()->toArray();
    	$childs = array_intersect($userchilds, $groupchilds);
    	
    	//Listado de niños que no están en el grupo y pertenecen al padre
    	$childsNoGroup = array_diff($userchilds, $childs);
    	    	
    	return array(
    			'childsNoGroup' => $childsNoGroup,
    			'childs' => $childs,
    			'user' => $user,
    			'group' => $group
    	);
    
    }
    
    /**
     * Edits an existing Groups entity.
     *
     * @Route("/{id}", name="panel_group_update")
     * @Method("PUT")
     * @Template("TrazeoBaseBundle:Groups:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($id);

        if (!$group) {
            throw $this->createNotFoundException('Unable to find Group entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($group);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('panel_group_edit', array('id' => $id)));
        }

        return array(
            'group'      => $group,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Groups entity.
     *
     * @Route("/{id}", name="panel_group_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($id);

            if (!$group) {
                throw $this->createNotFoundException('Unable to find Group entity.');
            }

            $em->remove($group);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('panel_group'));
    }

    /**
     * Creates a form to delete a Groups entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('panel_group_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
