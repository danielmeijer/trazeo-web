<?php
namespace Trazeo\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Trazeo\BaseBundle\Entity\ERoute;
use Trazeo\BaseBundle\Entity\EGroup;
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
    public function indexAction(Request $request)
    {
    	$em = $this->getDoctrine()->getManager();
    	$fos_user = $this->container->get('security.context')->getToken()->getUser();
    	$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        $routes = $em->getRepository('TrazeoBaseBundle:ERoute')->findAll();
        
        // City filter
        $city = null;
        if ($user->getCity() != null) $city = $user->getCity()->getId();
        if ($city == null) $city = "all";
        $cities = array();
        $iscity = false;
        foreach($routes as $route) {
        	if ($route->getCity() != null) {
        		if ($city == $route->getCity()->getId()) $iscity = true;
        		$cities[$route->getCity()->getId()] = $route->getCity();
        	}
        }
        if (!$iscity) $city = "all"; // if no cities for user
        // End City Filter        

        return array(
            'routes' => $routes,
        	'cities' => $cities,
        	'city' => $city
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
        $req=$request->request->all();
        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneById($req['trazeo_basebundle_route']['group']);
        $form = $this->createCreateForm($route, $group);
        $form->handleRequest($request);
	
        if ($form->isValid())
            $em = $this->getDoctrine()->getManager();
            $route->setAdmin($user);
            $em->persist($route);
            $em->flush();
            $group->setRoute($route);
            $em->persist($group);
            $em->flush();          
            $formData = $form->getData();
            $routeId = $formData->getId();

            return $this->redirect($this->generateUrl('panel_route_show',array('id'=>$routeId)));

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
    private function createCreateForm(ERoute $route,EGroup $group)
    {
    	$em = $this->getDoctrine()->getManager();
    	$spainCode = $em->getRepository('JJs\Bundle\GeonamesBundle\Entity\Country')->findOneByCode("ES");
    	$spainCodeId = $spainCode->getId();
    	
        $form = $this->createForm(new RouteType(), $route, array(
            'action' => $this->generateUrl('panel_route_create'),
            'method' => 'POST',
        	'attr' => array(
        				'Route.help.name' => $this->get('translator')->trans('Route.help.name'),
        				'Route.help.description' => $this->get('translator')->trans('Route.help.description'),
        				'Route.help.country' => $this->get('translator')->trans('Route.help.country'),
        				'default' => $spainCodeId
        		)
        ));
        $form->add('group',null,array('data'=>$group, 'attr'=>array('style'=>'display:none;')));
        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Routes entity.
     *
     * @Route("/{id}/new", name="panel_route_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $route = new ERoute();
        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneById($id);
        $form = $this->createCreateForm($route,$group);

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
        $fos_user = $this->container->get('security.context')->getToken()->getUser();
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        $groups = $reGroups->findByRoute($route);
        $cont = 0;
        foreach($groups as $group){
        	// Ver si estos niños van a ser un
        	$cont = $cont + $group->getChilds()->count();
        }
       
        if (!$route) {
            throw $this->createNotFoundException('Unable to find Route entity.');
        }
        
        $tutorialmap = 0;
        if(!$user->getTutorialMap()){
        	$user->setTutorialMap(1);
        	$em->persist($user);
        	$em->flush();
        	$tutorialmap = 1;
        }

        //$deleteForm = $this->createDeleteForm($id);
		//$location = $route->getLocation();
		//ldd($route->getPoints()->toArray());
        return array(
        	'user' => $user,
        	'tutorialmap' => $tutorialmap,
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
        	return $this->redirect($this->generateUrl('panel_groups'));
        }
        if (!$route) {
        	
        	$notification = $container->addFlashMessages("warning","No existe la ruta o ha sido eliminada");
        	return $this->redirect($this->generateUrl('panel_groups'));
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
    	$em = $this->getDoctrine()->getManager();
    	$spainCode = $em->getRepository('JJs\Bundle\GeonamesBundle\Entity\Country')->findOneByCode("ES");
    	$spainCodeId = $spainCode->getId();
    	
        $form = $this->createForm(new RouteType(), $route, array(
            'action' => $this->generateUrl('panel_route_update', array('id' => $route->getId())),
            'method' => 'PUT',
        	'attr' => array(
        				'Route.help.name' => $this->get('translator')->trans('Route.help.name'),
        				'Route.help.description' => $this->get('translator')->trans('Route.help.description'),
        				'Route.help.country' => $this->get('translator')->trans('Route.help.country'),
        				'default' => $spainCodeId
        		)
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    
    
    /* This is the static comparing function: */
    static function cmp_obj($a, $b)
    {
    	if ($al == $bl) {
    		return 0;
    	}
    	
    }
    /**
     * Finds and displays a Routes entity.
     *
     * @Route("/savemap", name="panel_save_map")
     */
    public function saveMapAction(Request $request)
    {
		$id = $request->get('id');
		$inputPoints = $request->get('inputPoints');
		$distance = $request->get('distanceInput');
		$city=$request->get('cityInput');
		$country=$request->get('countryInput');
		$points = explode(";", $inputPoints);

		$em = $this->getDoctrine()->getManager();

        $route = $em->getRepository('TrazeoBaseBundle:ERoute')->find($id);
        //$points = $em->getRepository('TrazeoBaseBundle:EPoints')->findByRoute($route->getId());
    	//ldd($request);
    	if(count($route->getPoints()->toArray()) != 0){
    		$pointsRm = $em->getRepository('TrazeoBaseBundle:EPoints')->findByRoute($route->getId());
    		
    		foreach($pointsRm as $pointRm){
    			$em->remove($pointRm);
    		}
    		$em->flush();
    	}
		for($i = 0;$i < count($points);$i++)
		{
			$latlng = explode(",", $points[$i]);

			$punto = new EPoints();
			$punto->setLocation(new SimplePoint($latlng[0], $latlng[1]));
			$punto->setRoute($route);
			$punto->setPickup($latlng[2]);
			$punto->setDescription($latlng[3]);
			$em->persist($punto);
		}
		
		$helper = $this->get('trazeo_base_helper');
		$city_entity = $helper->getCities($city, 10, true);
		$route->setDistance($distance);
		if (count($city_entity) > 0) {
			$route->setCity($city_entity[0]);	
			$route->setCountry($city_entity[0]->getCountry());
		}
		$em->persist($route);
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

            return $this->redirect($this->generateUrl('panel_route_show', array('id' => $id)));
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
        	return $this->redirect($this->generateUrl('panel_dashboard'));
        }
        
        $routeAdmin = $route->getAdmin();
        
		if($routeAdmin == $user){

			$em->remove($route);
			$em->flush();
			$notification = $container->addFlashMessages("success","La ruta ha sido eliminada");
			return $this->redirect($this->generateUrl('panel_dashboard'));
			
		}else {
			$notification = $container->addFlashMessages("error","Sólo el administrador puede eliminar una ruta");
			return $this->redirect($this->generateUrl('panel_dashboard'));	
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
