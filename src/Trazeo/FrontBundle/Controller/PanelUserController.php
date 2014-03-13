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
 * @Route("/panel/userextend")
 */
class PanelUserController extends Controller
{

    /**
     * Lists all UserExtend entities.
     *
     * @Route("/", name="panel_userextend")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('TrazeoBaseBundle:UserExtend')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new UserExtend entity.
     *
     * @Route("/", name="panel_userextend_create")
     * @Method("POST")
     * @Template("TrazeoFrontBundle:PanelUser:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new UserExtend();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('panel_userextend_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a UserExtend entity.
     *
     * @param UserExtend $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(UserExtend $entity)
    {
        $form = $this->createForm(new UserExtendType(), $entity, array(
            'action' => $this->generateUrl('panel_userextend_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new UserExtend entity.
     *
     * @Route("/new", name="panel_userextend_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new UserExtend();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a UserExtend entity.
     *
     * @Route("/{id}", name="panel_userextend_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrazeoBaseBundle:UserExtend')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserExtend entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing UserExtend entity.
     *
     * @Route("/{id}/edit", name="panel_userextend_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrazeoBaseBundle:UserExtend')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserExtend entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a UserExtend entity.
    *
    * @param UserExtend $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(UserExtend $entity)
    {
        $form = $this->createForm(new UserExtendType(), $entity, array(
            'action' => $this->generateUrl('panel_userextend_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing UserExtend entity.
     *
     * @Route("/{id}", name="panel_userextend_update")
     * @Method("PUT")
     * @Template("TrazeoFrontBundle:PanelUser:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrazeoBaseBundle:UserExtend')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserExtend entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);
       

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('panel_userextend_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a UserExtend entity.
     *
     * @Route("/{id}", name="panel_userextend_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TrazeoBaseBundle:UserExtend')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find UserExtend entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('panel_userextend'));
    }

    /**
     * Creates a form to delete a UserExtend entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('panel_userextend_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
