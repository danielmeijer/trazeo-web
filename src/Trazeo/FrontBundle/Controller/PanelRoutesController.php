<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Trazeo\BaseBundle\Entity\ERoute;
use Trazeo\BaseBundle\Entity\EPoints;
use Trazeo\BaseBundle\Form\RouteType;
use Sopinet\Bundle\SimplePointBundle\ORM\Type\SimplePoint;

/**
 * PanelRoutes controller.
 *
 * @Route("/panel/route")
 */
class PanelRoutesController extends Controller
{

    /**
     * Lists all Routes entities.
     *
     * @Route("/", name="panel_route")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $routes = $em->getRepository('TrazeoBaseBundle:ERoute')->findAll();

        return array(
            'routes' => $routes,
        );
    }
    
    /**
     * Lists all Routes entities.
     *
     * @Route("/{id}/current", name="panel_route_current")
     * @Method("GET")
     * @Template()
     */
    public function currentAction(ERoute $route)
    {
    	return array(
    		'route' => $route
    	);
    }
    
    public function lastEvent(){
    	$em = $this->getDoctrine()->getManager();
    	$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');
    	$events = $reEvent->findByAction("point", array('createdAt' => 'DESC'));
    	$lastEvent = $events[0];
    	return array(
    			'lastEvent' => $lastEvent
    	);
    }
    
    /**
     * Creates a new Route entity.
     *
     * @Route("/", name="panel_route_create")
     * @Method("POST")
     * @Template("TrazeoFrontBundle:Routes:new.html.twig")
     */
    public function createAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$fos_user = $this->container->get('security.context')->getToken()->getUser();
    	 
    	//UserExtend
    	$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
    	
        $route = new ERoute();
        $form = $this->createCreateForm($route);
        $form->handleRequest($request);
	
        if ($form->isValid())
            $em = $this->getDoctrine()->getManager();
            $route->setAdmin($user);
            $em->persist($route);
            $em->flush();

            $formData = $form->getData();
            $routeId = $formData->getId();

            return $this->redirect($this->generateUrl('panel_route_edit',array('id'=>$routeId)));

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
    private function createCreateForm(ERoute $route)
    {
        $form = $this->createForm(new RouteType(), $route, array(
            'action' => $this->generateUrl('panel_route_create'),
            'method' => 'POST',
        	'attr' => array(
        				'Route.help.name' => $this->get('translator')->trans('Route.help.name'),
        				'Route.help.country' => $this->get('translator')->trans('Route.help.country')
        		)
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Routes entity.
     *
     * @Route("/new", name="panel_route_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $route = new ERoute();

        $form = $this->createCreateForm($route);
        
        return array(
            'route' => $route,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Routes entity.
     *
     * @Route("/{id}", name="panel_route_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $route = $em->getRepository('TrazeoBaseBundle:ERoute')->find($id);
        $reGroups = $em->getRepository('TrazeoBaseBundle:EGroup');
        $groups = $reGroups->findByRoute($route);
        $cont = 0;
        foreach($groups as $group){
        	// Ver si estos niños van a ser un
        	$cont = $cont + $group->getChilds()->count();
        }
       
        if (!$route) {
            throw $this->createNotFoundException('Unable to find Route entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);
		//$location = $route->getLocation();
		//ldd($route->getPoints()->toArray());
        return array(
        	'cont'		  => $cont,
            'route'      => $route
            //'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Routes entity.
     *
     * @Route("/{id}/edit", name="panel_route_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $fos_user = $this->container->get('security.context')->getToken()->getUser();
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        $route = $em->getRepository('TrazeoBaseBundle:ERoute')->find($id);
        
        $userId = $user->getId();
        $routeAdmin = $route->getAdmin();
        $container = $this->get('sopinet_flashMessages');
        if($routeAdmin != $user ){
        
        	$notification = $container->addFlashMessages("error","No tienes autorización para editar esta ruta");
        	return $this->redirect($this->generateUrl('panel_route'));
        }
        if (!$route) {
        	
        	$notification = $container->addFlashMessages("warning","No existe la ruta o ha sido eliminada");
        	return $this->redirect($this->generateUrl('panel_route'));
        }

        $editForm = $this->createEditForm($route);

        return array(
            'route'      => $route,
            'edit_form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Routes entity.
    *
    * @param Routes $route
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ERoute $route)
    {
        $form = $this->createForm(new RouteType(), $route, array(
            'action' => $this->generateUrl('panel_route_update', array('id' => $route->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    /**
     * Finds and displays a Routes entity.
     *
     * @Route("/savemap", name="panel_save_map")
     */
    public function saveMapAction(Request $request)
    {
		//ldd($request);
		$id = $request->get('id');
		$inputPoints = $request->get('inputPoints');
		$points = explode(";", $inputPoints);

		$em = $this->getDoctrine()->getManager();

        $route = $em->getRepository('TrazeoBaseBundle:ERoute')->find($id);
        $points = $em->getRepository('TrazeoBaseBundle:EPoints')->findByRoute($route->getId());
    	ldd($points);
    	if(count($route->getPoints()->toArray()) != 0){
    		$points = $em->getRepository('TrazeoBaseBundle:EPoints')->findByRoute($route->getId());
    		foreach($points as $point){
    			$em->remove($point);
    		}
    	}
		for($i = 0;$i < count($points);$i++)
		{
			$latlng = explode(",", $points[$i]);

			$punto = new EPoints();
			$punto->setLocation(new SimplePoint($latlng[0], $latlng[1]));
			$punto->setRoute($route);
			$punto->setPickup($latlng[2]);
			$em->persist($punto);
		}
    	$em->flush();
    
    	return $this->redirect($this->generateUrl('panel_route_show', array('id' => $id)));
    }
    
    
    /**
     * Edits an existing Routes entity.
     *
     * @Route("/{id}", name="panel_route_update")
     * @Method("PUT")
     * @Template("TrazeoBaseBundle:Routes:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $route = $em->getRepository('TrazeoBaseBundle:ERoute')->find($id);

        if (!$route) {
            throw $this->createNotFoundException('Unable to find Route entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($route);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('panel_route_edit', array('id' => $id)));
        }

        return array(
            'route'      => $route,
            'edit_form'   => $editForm->createView(),
            //'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Routes entity.
     *
     * @Route("/{id}/delete", name="panel_route_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
    	
    	$fos_user = $this->container->get('security.context')->getToken()->getUser();
    	$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
    	$userId = $user->getId();

        $route = $em->getRepository('TrazeoBaseBundle:ERoute')->find($id);
        
        $container = $this->get('sopinet_flashMessages');
        
        if (!$route) {
        	$notification = $container->addFlashMessages("warning","La ruta que intentas eliminar no existe");
        	return $this->redirect($this->generateUrl('panel_route'));
        }
        
        $routeAdmin = $route->getAdmin();
        
		if($routeAdmin == $user){

			$em->remove($route);
			$em->flush();
			$notification = $container->addFlashMessages("success","La ruta ha sido eliminada");
			return $this->redirect($this->generateUrl('panel_route'));
			
		}else {
			$notification = $container->addFlashMessages("error","Sólo el administrador puede eliminar una ruta");
			return $this->redirect($this->generateUrl('panel_route'));	
		}
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
