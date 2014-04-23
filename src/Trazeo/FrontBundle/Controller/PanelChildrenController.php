<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\BaseBundle\Form\ChildType;

/**
 * Child controller.
 *
 * @Route("/panel/child")
 */
class PanelChildrenController extends Controller
{

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
        
        $childs = $user->getChilds();

        return array(
            'childs' => $childs,
        );
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

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $fos_user = $this->container->get('security.context')->getToken()->getUser();
            $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
            
            $user->addChild($child);
            
            $em->persist($child);
            $em->flush();

            return $this->redirect($this->generateUrl('panel_child'));
        }

        return array(
            'child' => $child,
            'form'   => $form->createView(),
        );
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
        
        if(count($users) == 0)throw $this->createNotFoundException('You have not permission');

        if (!$child) {
            throw $this->createNotFoundException('Unable to find Child entity.');
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
