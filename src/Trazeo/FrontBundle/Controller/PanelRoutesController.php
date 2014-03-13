<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Trazeo\BaseBundle\Entity\Routes;
use Trazeo\BaseBundle\Form\RoutesType;

/**
 * PanelRoutes controller.
 *
 * @Route("/panel/routes")
 */
class PanelRoutesController extends Controller
{

    /**
     * Lists all Routes entities.
     *
     * @Route("/", name="panel_routes")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('TrazeoBaseBundle:Routes')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Routes entity.
     *
     * @Route("/", name="panel_routes_create")
     * @Method("POST")
     * @Template("TrazeoFrontBundle:Routes:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Routes();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('panel_routes_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Routes entity.
     *
     * @param Routes $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Routes $entity)
    {
        $form = $this->createForm(new RoutesType(), $entity, array(
            'action' => $this->generateUrl('panel_routes_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Routes entity.
     *
     * @Route("/new", name="panel_routes_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
    	
        $entity = new Routes();
        $form   = $this->createCreateForm($entity);

        
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Routes entity.
     *
     * @Route("/{id}", name="panel_routes_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $reGroups = $em->getRepository('TrazeoBaseBundle:Groups');

        $entity = $em->getRepository('TrazeoBaseBundle:Routes')->find($id);
        $groups = $reGroups->findByRoutes($entity);
        
        
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Routes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
        	'groups'	  => $groups,
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Routes entity.
     *
     * @Route("/{id}/edit", name="panel_routes_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrazeoBaseBundle:Routes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Routes entity.');
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
    * Creates a form to edit a Routes entity.
    *
    * @param Routes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Routes $entity)
    {
        $form = $this->createForm(new RoutesType(), $entity, array(
            'action' => $this->generateUrl('panel_routes_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Routes entity.
     *
     * @Route("/{id}", name="panel_routes_update")
     * @Method("PUT")
     * @Template("TrazeoBaseBundle:Routes:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('TrazeoBaseBundle:Routes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Routes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('panel_routes_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Routes entity.
     *
     * @Route("/{id}", name="panel_routes_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('TrazeoBaseBundle:Routes')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Routes entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('panel_routes'));
    }

    /**
     * Creates a form to delete a Routes entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('panel_routes_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
