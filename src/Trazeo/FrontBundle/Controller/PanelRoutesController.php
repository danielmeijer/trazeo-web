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

        $routes = $em->getRepository('TrazeoBaseBundle:Routes')->findAll();

        return array(
            'routes' => $routes,
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
    	$em = $this->getDoctrine()->getManager();
    	$fos_user = $this->container->get('security.context')->getToken()->getUser();
    	 
    	//UserExtend
    	$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
    	
        $route = new Routes();
        $form = $this->createCreateForm($route);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $route->setAdmin($user);
            $em->persist($route);
            $em->flush();

            return $this->redirect($this->generateUrl('panel_routes_show', array('id' => $route->getId())));
        }

        return array(
            'route' => $route,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Routes entity.
     *
     * @param Routes $route The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Routes $route)
    {
        $form = $this->createForm(new RoutesType(), $route, array(
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
        $route = new Routes();

        $form = $this->createCreateForm($route);
        
        return array(
            'route' => $route,
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
        $route = $em->getRepository('TrazeoBaseBundle:Routes')->find($id);
        $reGroups = $em->getRepository('TrazeoBaseBundle:Groups');
        $groups = $reGroups->findByRoutes($route);
        $cont = 0;
        foreach($groups as $group){
        	// Ver si estos niños van a ser un
        	$cont = $cont + $group->getChildren()->count();
        }
       
        if (!$route) {
            throw $this->createNotFoundException('Unable to find Routes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
        	'cont'		  => $cont,
            'route'      => $route,
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

        $route = $em->getRepository('TrazeoBaseBundle:Routes')->find($id);

        if (!$route) {
            throw $this->createNotFoundException('Unable to find Routes entity.');
        }

        $editForm = $this->createEditForm($route);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'route'      => $route,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Routes entity.
    *
    * @param Routes $route The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Routes $route)
    {
        $form = $this->createForm(new RoutesType(), $route, array(
            'action' => $this->generateUrl('panel_routes_update', array('id' => $route->getId())),
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

        $route = $em->getRepository('TrazeoBaseBundle:Routes')->find($id);

        if (!$route) {
            throw $this->createNotFoundException('Unable to find Routes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($route);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('panel_routes_edit', array('id' => $id)));
        }

        return array(
            'route'      => $route,
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
            $route = $em->getRepository('TrazeoBaseBundle:Routes')->find($id);

            if (!$route) {
                throw $this->createNotFoundException('Unable to find Routes entity.');
            }

            $em->remove($route);
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
