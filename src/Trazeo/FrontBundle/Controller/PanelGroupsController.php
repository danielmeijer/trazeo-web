<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Trazeo\BaseBundle\Entity\EGroup;
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
        $userGroups = $user->getGroups();

        $allGroups = $em->getRepository('TrazeoBaseBundle:EGroup')->findAll();
        $groups = array_diff($allGroups,$userGroups->toArray());
        
        return array(
            'groups' => $groups,
        	'userGroups' => $userGroups
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
    	//ldd($group->getChilds()->toArray());
    	return array(
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
