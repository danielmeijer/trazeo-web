<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Trazeo\BaseBundle\Entity\Children;
use Trazeo\BaseBundle\Form\ChildrenType;

/**
 * Children controller.
 *
 * @Route("/panel/children")
 */
class PanelChildrenController extends Controller
{

    /**
     * Lists all Children entities.
     *
     * @Route("/", name="panel_children")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('TrazeoBaseBundle:Children')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Children entity.
     *
     * @Route("/", name="panel_children_create")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $entity = new Children();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $fos_user = $this->container->get('security.context')->getToken()->getUser();
            $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
            
            $user->addChild($entity);
            
            $em->persist($entity);
            $em->flush();

            //return $this->redirect($this->generateUrl('panel_children_show', array('id' => $entity->getId())));
            return $this->redirect($this->generateUrl('panel_children'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Children entity.
     *
     * @param Children $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Children $entity)
    {
        $form = $this->createForm(new ChildrenType(), $entity, array(
            'action' => $this->generateUrl('panel_children_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Children entity.
     *
     * @Route("/new", name="panel_children_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Children();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Children entity.
     *
     * @Route("/{id}/show", name="panel_children_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrazeoBaseBundle:Children')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Children entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Children entity.
     *
     * @Route("/{id}/edit", name="panel_children_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrazeoBaseBundle:Children')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Children entity.');
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
    * Creates a form to edit a Children entity.
    *
    * @param Children $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Children $entity)
    {
        $form = $this->createForm(new ChildrenType(), $entity, array(
            'action' => $this->generateUrl('panel_children_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Children entity.
     *
     * @Route("/{id}", name="panel_children_update")
     * @Method("PUT")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrazeoBaseBundle:Children')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Children entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('panel_children_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Children entity.
     *
     * @Route("/{id}/delete", name="panel_children_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, $id)
    {
        /*
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);
        */

        //if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TrazeoBaseBundle:Children')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Children entity.');
            }

            $em->remove($entity);
            $em->flush();
        //}

        return $this->redirect($this->generateUrl('panel_children'));
    }

    /**
     * Creates a form to delete a Children entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('panel_children_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
