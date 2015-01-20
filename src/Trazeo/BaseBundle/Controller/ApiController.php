<?php

namespace Trazeo\BaseBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\RouteRedirectView;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Trazeo\BaseBundle\Entity\EGroup;
use Trazeo\BaseBundle\Entity\UserExtend;

class ApiController extends Controller {

	#ToDo pasar los métodos comunes a un helper
	/**
	 * Funcion para representar un uso erroneo de la API
	 */
	private function msgDenied($msg=null) {
		$array['state'] = -1;
		if($msg!=null)$array['msg'] = $msg;
		else $array['msg'] = "Access Denied";
		return $array;
	}
	
	private function msgOk() {
		$view = view::create()
		->setStatusCode(200)
		->setData($this->doOk(null));
	
		return $this->handleView($view);
	}
	
	/**
	 * Funcion para representar un acceso valido a la API
	 * @param array $data Serie de datos
	 * @return array Serie de datos
	 */
	private function doOK($data) {
		$ret['state'] = 1;
		$ret['msg'] = "Ok";
		if($data == null) {
			$arr[] = null;
			$ret['data'] = $arr;
		}
		else
			$ret['data'] = $data;
		return $ret;
	}


	/**
	 * Funcion para manejar una excepcion
	 * @param exception
	 * @return view
	 */
	private function exceptionHandler($e) {
		$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied($e->getMessage()));

		return $this->get('fos_rest.view_handler')->handle($view);
	}

	/**
	 * Funcion que controla el usuario que envia datos a la API, sin estar logueado, con parámetros email y pass
	 */
	private function checkUser($email, $password){
	
		$user = $this->getDoctrine()->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array ("email"=>$email, "password"=>$password));
		//$user= $this->getDoctrine()->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array ("username"=>$email));		
		if ($user == null){
			$user = $this->getDoctrine()->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array ("username"=>$email, "password"=>$password));
			if ($user == null){
				return false;
			}
		}
		if ($password == $user->getPassword()){
			return $user;
		}
		else
			return false;
	}
	
	/**
	 * Funcion que controla si el usuario está logueado o se comprueba con su email y pass
	 */
	private function checkPrivateAccess(Request $request) {
		//$user = $this->get('security.context')->getToken()->getUser();
	
		/*if ($user != null && $user != "anon.") {
			return $user;
		}*/
	
		// TODO: ACTIVAR, QUITAR FALSE / NO HACE FALTA ESTA COMPROBACION
// 		if ('POST' != $request->getMethod() && false) {
// 			return false;
// 		}
	
		$user = $this->checkUser($request->get('email'), $request->get('pass'));
	
		//No es necesario
		if($user == false) {
			return false;
		}
	
		return $user;
	}


	/**
	 * Mandar la fecha del servior
	 * @POST("/api/timeStamp")
	 */
	public function getTimeStampAction(Request $request) {
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
			->setStatusCode(200)
			->setData($this->msgDenied());
	
			return $this->get('fos_rest.view_handler')->handle($view);
		}
        $now=new \Datetime();
 	 
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK($now->format('Y-m-d H:i:s')));
			
		return $this->get('fos_rest.view_handler')->handle($view);
	
	}	

    /**
	 * Crea un nuevo mensaje en el TimeLine (Muro) del Grupo
	 * 
	 * @POST("/api/group/timeline/notification")
	 * @param Request $request
     * Deprecated
	 */
	public function addNotificationAction(Request $request) {
	   	$em = $this->getDoctrine()->getManager();
	   	$fos_user = $this->container->get('security.context')->getToken()->getUser();	
	   	$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
	   	$comment = $em->getRepository('SopinetTimelineBundle:Comment')->findOneBy( array(), array('id' => 'DESC') );
	   	if($user!=null){
			$id_group = $request->get('id_group');
			$group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneById($id_group);
	   		$userextends = $group->getUserextendgroups()->toArray();
			$not = $this->container->get('sopinet_user_notification');
			foreach($userextends as $userextend)
			{
				$fos_reciver=$userextend->getUser();
	   			$url=$this->get('trazeo_base_helper')->getAutoLoginUrl($fos_reciver,'panel_group_timeline', array('id' => $group->getId()));	
				$not->addNotification(
					"timeline.newFromMonitor",
					"TrazeoBaseBundle:Userextend,SopinetTimelineBundle:Comment,TrazeoBaseBundle:EGroup",
					$user->getId().",".(($comment->getId())+1).",".$group->getId(),
					$url,
					$userextend->getUser(),
					null,
					$this->generateUrl('panel_group_timeline', array('id' => $group->getId()))
				);
			}	
			
	   	}
		$view = View::create()
		->setStatusCode(200)
		->setData($this->doOK("ok"));
			
		return $this->get('fos_rest.view_handler')->handle($view);	
	}

	/**
	 * @GET("/api/geo/city/list")
	 */
	public function getGeoCitiesAction() {
		$q = $this->getRequest()->get('q');

		$helper = $this->get('trazeo_base_helper');
		$cities = $helper->getCities($q);

        if($this->getRequest()->get('app')!=null&&$this->getRequest()->get('app')==true){
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->doOK($cities));
        }
        else{
            $view = View::create()
                ->setStatusCode(200)
                ->setData($cities);
        }
		return $this->get('fos_rest.view_handler')->handle($view);		
	}

    /**
     * @GET("/api/geo/countries/list")
     */
    public function getGeoCountriesAction() {
        $em = $this->get('doctrine.orm.entity_manager');
        $reCountries = $em->getRepository('JJsGeonamesBundle:Country');
        $query=$reCountries->createQueryBuilder('a');
        $query->select('a.name');

        $countries = $query->getQuery()->getArrayResult();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK($countries));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

	/**
	 * @POST("/api/exchange/code")
	 */
	public function exchangeCodeAction() {
		$user = $this->get('security.context')->getToken()->getUser();
		$q = $this->getRequest()->get('code');
		$em = $this->get('doctrine.orm.entity_manager');
		$code=$this->container->getParameter('exchange_code');
	    //Obtener usuarios que tengan marcada la opcion de conexion con civiclub
        $reUserValue = $em->getRepository("SopinetUserPreferencesBundle:UserValue");
        $civiclub_setting = $em->getRepository("SopinetUserPreferencesBundle:UserSetting")->findOneByName("civiclub_conexion");
		if($q==$code){
			$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
			$sopinetuserextend=$em->getRepository("SopinetUserBundle:SopinetUserExtend")->findOneByUser($userextend->getUser());
	    	//Añadimos los puntos por crear el usuario
            $container = $this->get('sopinet_gamification');
        	if($container->addUserAction(
        		"Create_User",
        		"TrazeoBaseBundle:UserExtend",
        		$userextend->getId(),
        		$userextend,
        		1,
        		false
        	)!=null){
        		$view = View::create()
				->setStatusCode(200)
				->setData($userextend->getPoints());
        	}
        	else{
				$view = View::create()
				->setStatusCode(200)
				->setData("false");
        	}
		}
		else{
			$view = View::create()
			->setStatusCode(200)
			->setData("false");
		}
		return $this->get('fos_rest.view_handler')->handle($view);		
	}

	/**
	 * @GET("/api/cities")
	 */
	public function getCitiesAction(Request $request) {
		$user = $this->checkPrivateAccess($request);
		$em = $this->get('doctrine.orm.entity_manager');
		$userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);

		$reJJ = $em->getRepository("JJsGeonamesBundle:City");
		$groups = $em->getRepository('TrazeoBaseBundle:EGroup')->findAll();
		
		$cities=[];
		if($userextend)$userCity=$userextend->getCity();
		else $userCity=null;
		$userCity=$reJJ->findOneById($userCity);
		if($userCity!=null)$userCity=$userCity->getNameUtf8();
		$info=[];
		foreach ($groups as $group) {
					$city=$reJJ->findOneById($group->getCity());
					if($city!=null && !in_array($city->getNameUtf8(),$cities))$cities[]=$city->getNameUtf8();
		}
		$info['cities']=$cities;
		$info['userCity']=$userCity;
		$response = json_encode($info);

		return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));
	}

    /**
     * Método que devuelve una url de autologin a la ruta indicada
     * @POST("/api/auto/url")
     */
    public function getAutoUrlAction(Request $request){
        $user = $this->checkPrivateAccess($request);
        if( $user == false || $user == null ){
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $route=$request->get('route');
        $parameters=$request->get('parameters');
        $url=$this->container->getParameter('base_url').$this->get('trazeo_base_helper')->getAutoLoginUrl($user,$route,$parameters);

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK($url));
        return $this->get('fos_rest.view_handler')->handle($view);
    }

	/**
	 * Método que devuelve el listado de ciudades con catalogo de ofertas
	 * @GET("/api/catalog/cities")
	 */
	public function getCitiesWithCatalogAction(Request $request) {
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
				->setStatusCode(200)
				->setData($this->msgDenied());

			return $this->get('fos_rest.view_handler')->handle($view);
		}
		$em = $this->get('doctrine.orm.entity_manager');

		//Ciudades que estan dentro del catalogo
		$catalogItems=$em->getRepository('TrazeoBaseBundle:ECatalogItem')->findAll();
		$cities=[];
		foreach($catalogItems as $item){
			//Obtenemos todas las ciudades con catalogo
			if($item->getCitys()!=null && !in_array($item->getCitys()->getNameUtf8(),$cities))
				$cities[]=$item->getCitys()->getNameUtf8();
		}
		$view = View::create()
			->setStatusCode(200)
			->setData($this->doOK($cities));
		return $this->get('fos_rest.view_handler')->handle($view);
	}

	/**
	 * Método que devuelve el listado de ciuades con catalogo de ofertas
	 * @GET("/api/catalog/city")
	 */
	public function getCatalogCitiesAction(Request $request) {
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
				->setStatusCode(200)
				->setData($this->msgDenied());

			return $this->get('fos_rest.view_handler')->handle($view);
		}
		$em = $this->get('doctrine.orm.entity_manager');

		$cityName=$request->get('city');
		//Ofertas del catalogo local
		$city=$em->getRepository('JJsGeonamesBundle:City')->findOneByNameUtf8($cityName);
		$catalogItems = $em->getRepository('TrazeoBaseBundle:ECatalogItem')->findByCitys($city);
		$info['local']=$catalogItems;
		//Ofertas del catalogo web
		$catalogItems = $em->getRepository('TrazeoBaseBundle:ECatalogItem')->findByCitys(null);
		$info['internet']=$catalogItems;
		$view = View::create()
			->setStatusCode(200)
			->setData($this->doOK($info));
		return $this->get('fos_rest.view_handler')->handle($view);
	}


	/**
	 * Función para solicitar el canjeo de una oferta
	 * @param Request $request
	 * @return Response
	 * @Post("/api/catalog/exchange")
     */
	public function postExchangeAction(Request $request)
	{
		$user = $this->checkPrivateAccess($request);
		if( $user == false || $user == null ){
			$view = View::create()
				->setStatusCode(200)
				->setData($this->msgDenied());

			return $this->get('fos_rest.view_handler')->handle($view);
		}
		$em = $this->get('doctrine.orm.default_entity_manager');
		$repositoryCatalogItem=$em->getRepository('TrazeoBaseBundle:ECatalogItem');
		/** @var UserExtend $user */
		$user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
		try{
			$message=$repositoryCatalogItem->exchangeCatalogItem($user,$request->get('id_catalog_item'));
		}catch (PreconditionFailedHttpException $e){
			return $this->exceptionHandler($e);
		}catch (NotFoundHttpException $e){
			return $this->exceptionHandler($e);
		}
		$this->container->get('mailer')->send($message);
		$view = View::create()
			->setStatusCode(200)
			->setData($this->doOK('ok'));
		return $this->get('fos_rest.view_handler')->handle($view);
	}
}