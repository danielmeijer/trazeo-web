<?php

namespace Trazeo\BaseBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sopinet\Bundle\ChatBundle\Entity\Chat;
use Sopinet\Bundle\ChatBundle\Entity\ChatRepository;
use Sopinet\Bundle\ChatBundle\Service\ApiHelper;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\View\RouteRedirectView;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Trazeo\BaseBundle\Entity\ERide;
use Trazeo\BaseBundle\Entity\EGroup;
use Trazeo\BaseBundle\Entity\EGroupInvite;
use Trazeo\BaseBundle\Entity\EGroupAccess;
use Sopinet\Bundle\SimplePointBundle\ORM\Type\SimplePoint;
use Sopinet\TimelineBundle\Entity\Comment;

class ApiGroupController extends Controller
{

    /**
     * Funcion para representar un uso erroneo de la API
     * @param null $msg
     *
     * @return array $array
     */
    private function msgDenied($msg = null)
    {
        $array['state'] = -1;
        if ($msg != null) {
            $array['msg'] = $msg;
        } else {
            $array['msg'] = "Access Denied";
        }

        return $array;
    }

    private function msgOk()
    {
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
    private function doOK($data)
    {
        $ret['state'] = 1;
        $ret['msg'] = "Ok";
        if ($data == null) {
            $arr[] = null;
            $ret['data'] = $arr;
        } else {
            $ret['data'] = $data;
        }

        return $ret;
    }


    /**
     * Funcion para manejar una excepcion
     * @param exception
     * @return view
     */
    private function exceptionHandler($e)
    {
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
        if ($user == null) {
            $user = $this->getDoctrine()->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array ("username"=>$email, "password"=>$password));
            if ($user == null) {
                return false;
            }
        }
        if ($password == $user->getPassword()) {
            return $user;
        } else {
            return false;
        }
    }

    /**
     * Funcion que controla si el usuario está logueado o se comprueba con su email y pass
     */
    private function checkPrivateAccess(Request $request)
    {
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
        if ($user == false) {
            return false;
        }

        return $user;
    }

    /**
     * Devuelve una vista filtrada por un Grupo
     *
     * @return mixed
     */
    public function msgByGroup($msg, $groups, $status=200) {
        $view = view::create()
            ->setStatusCode($status)
            ->setSerializationContext(SerializationContext::create()->setGroups($groups))
            ->setData($this->doOk($msg));

        return $this->viewhandler->handle($view);
    }

    /**
     * @param Request $request
     *
     * @return View|Response
     * @ApiDoc(
     *   description="Función que borra un grupo",
     *   section="group",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="pass", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *      {"name"="group_id", "dataType"="string", "required"=true, "description"="id del grupo"},
     *   }
     * )
     * @POST("/api/deleteGroup")
     */
    public function deleteGroupAction(Request $request)
    {
        //Comprobamos las credenciales de usuaio
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $idGroup = $request->get('id_group');
        $em = $this->get('doctrine.orm.entity_manager');
        $userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
        //Obtenemos el repositorio del grupo
        $repositoryGroup = $em->getRepository('TrazeoBaseBundle:EGroup');
        //Intentamos borrar el grupo
        try {
            $repositoryGroup->userDeleteGroup($idGroup, $userextend);
            //Grupo borrado con exito
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->doOK('ok'));

            return $this->get('fos_rest.view_handler')->handle($view);
        } catch (PreconditionFailedHttpException $e) {
            //no encontramos el grupo
            return $this->exceptionHandler($e);
        } catch (AccessDeniedException $e) {
            //el usuario no es el administrador del grupo
            return $this->exceptionHandler($e);
        }
    }


    /**
     * @param Request $request
     *
     * @return Response
     * @POST("/api/groups")
     * @ApiDoc(
     *   description="Función que devuelve el listado de grupos de un usuario",
     *   section="group",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="pass", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *   },
     *  output="array<Trazeo\BaseBundle\Entity\EGroup>"
     *  )
     */
    public function getGroupsAction(Request $request)
    {

        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
        //Se escogen los grupos del usuario, si la llamada se hizo con el parametro admin
        // solo se devolveran aquellos en los que es admin
        $groups = $userextend->getGroups();
        $admingroups = $em->getRepository('TrazeoBaseBundle:EGroup')->findByAdmin($userextend);

        $array = array();
        foreach ($groups as $group) {
            $arrayGroups = array();
            /** @var EGroup $group */
            $arrayGroups['id'] = $group->getId();
            $arrayGroups['name'] = $group->getName();
            $arrayGroups['visibility'] = $group->getVisibility();
            $arrayGroups['hasride'] = $group->getHasRide();
            $arrayGroups['isMonitor'] = $group->isMonitor($userextend);
            if ($group->getCity()) {
                $arrayGroups['city'] = $group->getCity()->getNameUtf8();
            }
            $arrayGroups['school'] = $group->getSchool1();
            $arrayGroups['admin'] = in_array($group, $admingroups);
            if ($group->getHasRide() == 1) {
                $ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneByGroup($group);
                $arrayGroups['ride_id'] = $ride->getId();
            }
            $array[] = $arrayGroups;
        }
        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK($array));

        return $this->get('fos_rest.view_handler')->handle($view);
    }


    /**
     * @param Request $request
     *
     * @return Response
     * @ApiDoc(
     *   description="Petición para crear un grupo en la bbdd, o modificar uno existente",
     *   section="group",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="pass", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *      {"name"="name", "dataType"="string", "required"=true, "description"="Nombre del grupo"},
     *      {"name"="visibility", "dataType"="string", "required"=true, "description"="Visibilidad del grupo"},
     *      {"name"="school1", "dataType"="string", "required"=true, "description"="Nombre del centro escolar"},
     *      {"name"="country", "dataType"="string", "required"=true, "description"="Pais del grupo"},
     *      {"name"="city", "dataType"="string", "required"=true, "description"="Ciudad del grupo"},
     *      {"name"="id_grupo", "dataType"="string", "required"=true, "description"="Id del grupo a modificar"},
     *   },
     *  output="Integer"
     *  )
     * Petición para crear un grupo en la bbdd
     * @POST("/api/manageGroup")
     */
    public function manageGroupAction(Request $request)
    {

        $name = $request->get('name');
        $visibility = $request->get('visibility');
        $idGroup = $request->get('id_group');
        $school1 = $request->get('school1');
        $country = $request->get('country');

        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }

        $em = $this->get('doctrine.orm.entity_manager');
        $userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);

        //Se comprueba si ya existe otro grupo con el mismo nombre
        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneByName($name);
        if ($group != null && $group != $em->getRepository('TrazeoBaseBundle:EGroup')->find($idGroup)) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied("Name is already in use"));

            return $this->get('fos_rest.view_handler')->handle($view);
        }

        //Si el grupo existe se modifica si no se crea uno nuevo
        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneBy(array('id' => $idGroup, 'admin' => $userextend));

        //Si el usuario no es el admin del grupo y lo intenta modificar se deniega el ascesso
        if ($idGroup != null && $group == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied("User is not the admin"));

            return $this->get('fos_rest.view_handler')->handle($view);
        } //Si el grupo no existe se crea
        else if ($group == null) {
            $group = new EGroup();
            $group->addUserextendgroup($userextend);
            $group->setAdmin($userextend);
            //Children autojoin on parent create group
            $childs = $userextend->getChilds();
            foreach ($childs as $child) {
                $group->addChild($child);
            }
            //Add points for create frist group
            $sopinetuserextend = $em->getRepository("SopinetUserBundle:SopinetUserExtend")->findOneByUser($userextend);
            $container = $this->get('sopinet_gamification');
            $container->addUserAction(
                "Create_Group",
                "TrazeoBaseBundle:UserExtend",
                $userextend->getId(),
                $userextend,
                1,
                false
            );
            $group->setName($name);
            $group->setVisibility($visibility);
            $group->setSchool1($school1);
            $em->persist($group);
            $em->flush();
        }
        if ($request->get('city')) {
            $city = $request->get('city');
            $helper = $this->get('trazeo_base_helper');
            $cityEntity = $helper->getCities($city, 10, true);
            if (count($cityEntity) > 0) {
                $group->setCity($cityEntity[0]);
            }
        }
        $reGroup = $em->getRepository('TrazeoBaseBundle:EGroup')->setCountry($group->getId(), $country);

        $group->setName($name);
        $group->setVisibility($visibility);
        $group->setSchool1($school1);
        $em->persist($group);
        $em->flush();

        //Se devuelve el id del grupo
        $data['id'] = $group->getId();
        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK($data));

        return $this->get('fos_rest.view_handler')->handle($view);

    }


    /**
     * @param Request $request
     *
     * @return Response
     * @ApiDoc(
     *   description="Funcion que devuelve todos los grupos de una ciudad",
     *   section="group",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="pass", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *      {"name"="city", "dataType"="string", "required"=true, "description"="Ciudad del grupo|all todas"},
     *      {"name"="object", "dataType"="string", "required"=false, "description"="Si es para web o app"},
     *   },
     *  output="Integer"
     *  )
     * @GET("/api/groupsCity")
     */
    public function getGroupsByCityAction(Request $request)
    {
        $city = $this->getRequest()->get('city');
        $object = $this->getRequest()->get('object');
        $em = $this->get('doctrine.orm.entity_manager');
        $user = $this->checkPrivateAccess($request);
        $userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);

        $helper = $this->get('trazeo_base_helper');
        $cities = $helper->getCities($city);

        $admingroups = $em->getRepository('TrazeoBaseBundle:EGroup')->findByAdmin($userextend);

        $names = [];
        //Para todas las ciudades
        if ($city == 'all') {
            $groups = $em->getRepository('TrazeoBaseBundle:EGroup')->findAll();
            foreach ($groups as $group) {
                if (!$object) {
                    $names['data']['groups'][] = $group->getName();
                    $names['data']['ids'][] = $group->getId();
                } else {
                    $arrayGroups = array();
                    $arrayGroups['id'] = $group->getId();
                    $arrayGroups['name'] = $group->getName();
                    if ($group->getRoute() != null) {
                        $arrayGroups['route']['name'] = $group->getRoute()->getName();
                        $arrayGroups['route']['admin_name'] = $group->getAdmin()->__toString();
                    }
                    $arrayGroups['visibility'] = $group->getVisibility();
                    $arrayGroups['hasride'] = $group->getHasRide();
                    $arrayGroups['school'] = $group->getSchool1();
                    $arrayGroups['admin'] = in_array($group, $admingroups);
                    if ($group->getHasRide() == 1) {
                        $ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneByGroup($group);
                        $arrayGroups['ride_id'] = $ride->getId();
                    }
                    $names['data'][] = $arrayGroups;
                }
            }
            $response = json_encode($names);

            return new Response($response, 200, array(
                'Content-Type' => 'application/json'
            ));
        }
        //solo para la ciudad indicada
        //compatibilidad con grupos sin ciudad
        $routes = $em->getRepository('TrazeoBaseBundle:ERoute')->findByCity($cities[0]);
        $aux = array();
        foreach ($routes as $route) {
            $group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneByRoute($route);
            if ($group != null) {
                $aux[] = $group;
            }
        }
        $groups = $em->getRepository('TrazeoBaseBundle:EGroup')->findByCity($cities[0]);
        $groups = array_merge($groups, $aux);
        $groups = array_unique($groups);

        foreach ($groups as $group) {
            if ($group != null && !$object) {
                $names['data']['groups'][] = $group->getName();
                $names['data']['ids'][] = $group->getId();
            } else if ($group != null) {
                $arrayGroups = array();
                $arrayGroups['id'] = $group->getId();
                $arrayGroups['name'] = $group->getName();
                if ($group->getRoute() != null) {
                    $arrayGroups['route']['name'] = $group->getRoute()->getName();
                    $arrayGroups['route']['admin_name'] = $group->getAdmin()->__toString();
                }
                $arrayGroups['visibility'] = $group->getVisibility();
                $arrayGroups['hasride'] = $group->getHasRide();
                if (in_array($group, $admingroups)) {
                    $arrayGroups['admin'] = true;
                } else {
                    $arrayGroups['admin'] = false;
                }
                if ($group->getHasRide() == 1) {
                    $ride = $em->getRepository('TrazeoBaseBundle:ERide')->findOneByGroup($group);
                    $arrayGroups['ride_id'] = $ride->getId();
                }
                $names['data'][] = $arrayGroups;
            }
        }

        $response = json_encode($names);

        return new Response($response, 200, array(
            'Content-Type' => 'application/json'
        ));
    }

    /**
     * @param Request $request
     *
     * @return Response
     *
     * @ApiDoc(
     *   description="Llamada que une un usuario a un grupo",
     *   section="group",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="pass", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *      {"name"="id_group", "dataType"="string", "required"=true, "description"="Id del grupo al que quiere unirse"},
     *   },
     *  output="Boolean"
     *  )
     * @POST("/api/joinGroup")
     */
    public function joinGroupAction(Request $request)
    {
        //Comprobamos las credenciales del usuario
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $idGroup = $request->get('id_group');

        $em = $this->getDoctrine()->getManager();
        $userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
        $groupRepository = $em->getRepository('TrazeoBaseBundle:EGroup');
        //Unimos el usuario al grupo
        try {
            $groupRepository->joinGroup($idGroup, $userextend);
            $info['joined'] = 'true';
            $response = json_encode($info);

            return new Response($response, 200, array(
                'Content-Type' => 'application/json'
            ));
        } catch (Exception $e) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied($e->getMessage()));

            return $this->get('fos_rest.view_handler')->handle($view);
        }
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @ApiDoc(
     *   description="Llamada para solicitar el acesso a un grupo",
     *   section="group",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="pass", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *      {"name"="id_grupo", "dataType"="string", "required"=true, "description"="Id del grupo al que quiere unirse"},
     *   },
     *  output="Boolean"
     *  )
     * @POST("/api/requestJoinGroup")
     */
    public function requestJoinGroupAction(Request $request)
    {
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $id = $request->get('id_group');
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);

        $userId = $user->getId();

        // Obtener grupo al que se quiere unir a través del param $id
        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($id);
        if (!$group) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied("Group doesn't exist"));

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $groupId = $group->getId();

        $groupVisibility = $group->getVisibility();
        // Buscar si existe alguna petición con ese UserExtend y ese Group
        $requestUser = $em->getRepository('TrazeoBaseBundle:EGroupAccess')->findOneByUserextend($user);
        $requestGroup = $em->getRepository('TrazeoBaseBundle:EGroupAccess')->findOneByGroup($group);
        $container = $this->get('sopinet_flashMessages');
        if ($groupVisibility == 2) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied("The group is hidden"));

            return $this->get('fos_rest.view_handler')->handle($view);
        } else if ($groupVisibility == 0) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied("The group is public"));

            return $this->get('fos_rest.view_handler')->handle($view);
        } //comprobar que el user no este vinculado ya al grupo
        else if (in_array($group, $user->getGroups()->toArray())) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied("User it's already on group"));

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        // Comprobar que existen
        if ($requestUser && $requestGroup == true) {

            // Si existen, obtener el id de su registro en la base de datos
            $requestUserId = $requestUser->getId();
            $requestGroupId = $requestGroup->getId();
            // Comprobar que no tienen el mismo id de registro (petición duplicada)
            if ($requestUserId = $requestGroupId) {
                // Excepción y redirección
                $view = View::create()
                    ->setStatusCode(200)
                    ->setData($this->msgDenied("Join to group request has been did before"));

                return $this->get('fos_rest.view_handler')->handle($view);
            }

        } else {
            // Si no existen los UserExtend y Group anteriormente obtenidos,
            // directamente se crea la petición
            $groupAdmin = $group->getAdmin();
            $groupAdminUser = $em->getRepository('TrazeoBaseBundle:UserExtend')->find($groupAdmin);

            $fos_user_admin = $groupAdminUser->getUser();
            //ldd($fos_user_admin);
            $url = $this->get('trazeo_base_helper')->getAutoLoginUrl($groupAdminUser->getUser(), 'panel_group');
            $not = $this->container->get('sopinet_user_notification');
            $el = $not->addNotification(
                'group.join.request',
                "TrazeoBaseBundle:UserExtend,TrazeoBaseBundle:EGroup",
                $userId . "," . $groupId,
                $url,
                $groupAdminUser->getUser(),
                null,
                $this->generateUrl('panel_group')
            );

            $el->setImportant(1);
            $em->persist($el);
            $em->flush();

            $access = new EGroupAccess();
            $access->setGroup($group);
            $access->setUserextend($user);

            $em->persist($access);
            $em->flush();
            $info['request'] = 'true';
            $response = json_encode($info);

            return new Response($response, 200, array(
                'Content-Type' => 'application/json'
            ));


        }

    }


    /**
     * @param Request $request
     *
     * @return Response
     * @ApiDoc(
     *   description="Llamada para invitar a un usuario  a un grupo",
     *   section="group",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="pass", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *      {"name"="id_grupo", "dataType"="string", "required"=true, "description"="Id del grupo al que quiere unirse"},
     *      {"name"="email_invite", "dataType"="string", "required"=true, "description"="Email del usuario a invitar"},
     *   },
     *  output="Boolean"
     *  )
     * @POST("/api/group/invite")
     */
    public function groupInviteAction(Request $request)
    {
        // TODO: PASAR FUNCION A UN SERVICIO; Se está usando en ApiController y PanelGroupsController
        // TODO: Hay que arreglar esta función, no devuelve un JSON correctamente..
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }


        $em = $this->get('doctrine.orm.entity_manager');

        $um = $this->container->get('fos_user.user_manager');

        $container = $this->get('sopinet_flashMessages');

        $fos_user_current = $user;

        $userEmail = $request->get('email_invite');
        $groupId = $request->get('id_group');

        $fos_user = $um->findUserByEmail($userEmail);
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);

        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($groupId);
        $groupUsers = $group->getUserextendgroups();

        foreach ($groupUsers as $groupUser) {
            if ($user == $groupUser) {

                $view = View::create()
                    ->setStatusCode(200)
                    ->setData($this->msgDenied("The user is already part of the group"));

                return $this->get('fos_rest.view_handler')->handle($view);
            }
        }


        if ($fos_user != true) {
            // Si el usuario no está registrado, habrá que registrarlo
            $reGAI = $em->getRepository('TrazeoBaseBundle:EGroupAnonInvite');
            $reGAI->createNew($group, $userEmail, $fos_user_current, $this);

            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->doOK());

            return $this->get('fos_rest.view_handler')->handle($view);
        }

        if ($fos_user == $fos_user_current) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied("User is the admin of the group"));

            return $this->get('fos_rest.view_handler')->handle($view);
        }

        // Obtener grupo al que se va a unir a través del param $id
        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($groupId);

        // Buscar si existe alguna petición con ese UserExtend y ese Group
        $requestUser = $em->getRepository('TrazeoBaseBundle:EGroupInvite')->findOneByUserextend($user);
        $requestGroup = $em->getRepository('TrazeoBaseBundle:EGroupInvite')->findOneByGroup($group);

        // Comprobar que existen
        if ($requestUser && $requestGroup == true) {

            // Si existen, obtener el id de su registro en la base de datos
            $requestUserId = $requestUser->getId();
            $requestGroupId = $requestGroup->getId();
            // Comprobar que no tienen el mismo id de registro (petición duplicada)
            if ($requestUserId = $requestGroupId) {
                // Excepción y redirección
                $view = View::create()
                    ->setStatusCode(200)
                    ->setData($this->msgDenied("Duplicate request"));

                return $this->get('fos_rest.view_handler')->handle($view);
            }
        }
        // Si no existen los UserExtend y Group anteriormente obtenidos,
        // directamente se crea la petición
        $user_current = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user_current->getId());
        $url = $this->get('trazeo_base_helper')->getAutoLoginUrl($fos_user, 'panel_group');
        $not = $this->container->get('sopinet_user_notification');
        $el = $not->addNotification(
            'group.invite.user',
            "TrazeoBaseBundle:UserExtend,TrazeoBaseBundle:EGroup",
            $user_current->getId() . "," . $groupId,
            $url,
            $fos_user,
            null,
            $this->generateUrl('panel_group')
        );
        $el->setImportant(1);
        $em->persist($el);
        $em->flush();

        $access = new EGroupInvite();
        $access->setGroup($group);
        $access->setUserextend($user);
        $access->setSender($user_current);

        $em->persist($access);
        $em->flush();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK());

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @ApiDoc(
     *   description="Llamada para salirse de un grupo",
     *   section="group",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="pass", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *      {"name"="id_group", "dataType"="string", "required"=true, "description"="Id del grupo del que quiere salirse"},
     *   },
     *  output="Boolean"
     *  )
     * @POST("/api/group/disjoin")
     */
    public function disjoinGroupAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
        $idGroup = $request->get('id_group');
        $reGroup = $em->getRepository('TrazeoBaseBundle:EGroup');
        //Desvinculamos al usuario del grupo
        try {
            $reGroup->disjoinGroup($idGroup, $userextend);
        } catch (\Exception $e) {
            $this->exceptionHandler($e);
        }

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK('ok'));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @param Request $request
     *
     * @return View|Response
     * @ApiDoc(
     *   description="Función que une/saca a un niño de un grupo ",
     *   section="group",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="pass", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *      {"name"="id_child", "dataType"="string", "required"=true, "description"="id del niño"},
     *      {"name"="id_group", "dataType"="string", "required"=true, "description"="id del grupo"},
     *      {"name"="add", "dataType"="string", "required"=false, "description"="Indica si se une o sale del grupo true|false"},
     *   }
     * )
     * @POST("/api/group/setChild")
     */
    public function joinChildGroupAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }

        $userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
        $idGroup = $request->get('id_group');
        $idChild = $request->get('id_child');
        $add = $request->get('add')=="true";
        $reGroup = $em->getRepository('TrazeoBaseBundle:EGroup');
        try {
            $reGroup->setChildOnGroup($idGroup, $idChild, $userextend, $add);
        } catch (PreconditionFailedHttpException $e) {
            return $this->exceptionHandler($e);
        }

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK('ok'));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @param Request $request
     *
     * @return View|mixed
     *
     * @ApiDoc(
     *   description="Función que crea un chat para un grupo, o si ya existe lo devuelve",
     *   section="group",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="pass", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *      {"name"="device_token", "dataType"="string", "required"=true, "description"="Token del dispositivo"},
     *      {"name"="group_id", "dataType"="string", "required"=true, "description"="id del grupo"},
     *   }
     * )
     *
     * @POST("/api/group/create/chat")
     */
    public function postCreateChatAction(Request $request)
    {
        $apiHelper=$this->get('apihelper');
        //Comprobamos el usuario
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $em=$this->get('doctrine.orm.default_entity_manager');
        $repositoryUserExtend=$this->get('doctrine.orm.default_entity_manager')->getRepository('TrazeoBaseBundle:UserExtend');
        $userextend = $repositoryUserExtend->findOneByNick($user->getEmail());
        //se comprueba el device
        $token=$request->get('device_token');
        $repositoryDevice = $em->getRepository('SopinetGCMBundle:Device');
        $device=$repositoryDevice->findOneBy(array('user'=>$userextend,'token'=>$token));
        if ($device==null) {
            return $apiHelper->msgDenied(ApiHelper::NODEVICE);
        }
        //Obtenemos el grupo
        $repositoryGroup = $em->getRepository('TrazeoBaseBundle:EGroup');
        $group=$repositoryGroup->find($request->get('group_id'));
        //Comprobamos si el usuario pertenece al grupo
        if (!$repositoryGroup->isUserInGroup($userextend, $group)) {
            $apiHelper->msgDenied('User is not in the group');
        }
        /** @var ChatRepository $repositoryChat */
        $repositoryChat = $em->getRepository('SopinetChatBundle:Chat');
        //Comprobamos si el grupo ya tiene chat
        if ($group->getChat()!=null) {
            $chat=$group->getChat();
            if (!$repositoryChat->userInChat($userextend, $chat)) {
                //Si el usuario no esta en el chat se añade
                try {
                    $repositoryChat->addMember($chat, $userextend->getId());
                } catch (\Exception $e) {
                    return $this->exceptionHandler($e);
                }
            }
        }
        //Si no lo tiene se crea
        else{
            $chat=new Chat();
            $chat->setType(Chat::EVENT);
            $chat->setName($group->getName());
            $chat->setAdmin($userextend);
            //Añadimos todos los user del grupo en el chat
            foreach ($group->getUserextendgroups() as $user) {
                try {
                    $repositoryChat->addMember($chat, $user->getId());
                } catch (\Exception $e) {
                    return $this->exceptionHandler($e);
                }
            }

        }
        $group->setChat($chat);
        $em->persist($chat);
        $em->persist($group);
        $em->flush();

        return $apiHelper->msgOK($chat);
    }



    /**
     * @param Request $request
     *
     * @return View|mixed
     *
     * @ApiDoc(
     *   description="Función que devuelve el listado de usuarios de un chat",
     *   section="chat",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="pass", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *      {"name"="group_id", "dataType"="string", "required"=true, "description"="id del grupo"},
     *   }
     * )
     *
     * @POST("/api/group/userList/chat")
     */
    public function postGetUserListChatAction(Request $request)
    {
        $apiHelper=$this->get('apihelper');
        //Comprobamos el usuario
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $em=$this->get('doctrine.orm.default_entity_manager');
        $repositoryUserExtend=$this->get('doctrine.orm.default_entity_manager')->getRepository('TrazeoBaseBundle:UserExtend');
        $userextend = $repositoryUserExtend->findOneByNick($user->getEmail());
        //Obtenemos el grupo
        $repositoryGroup = $em->getRepository('TrazeoBaseBundle:EGroup');
        /** @var EGroup $group */
        $group=$repositoryGroup->find($request->get('group_id'));
        //Comprobamos si el usuario pertenece al grupo
        if (!$repositoryGroup->isUserInGroup($userextend, $group)) {
            $apiHelper->msgDenied('User is not in the group');
        }
        /** @var ChatRepository $repositoryChat */
        $repositoryChat = $em->getRepository('SopinetChatBundle:Chat');
        //Comprobamos si el grupo tiene chat
        if ($group->getChat()!=null) {
            $members=$group->getChat()->getChatMembers();
        } else {
            $apiHelper->msgDenied("Chat doesn't exist");
        }

        return $apiHelper->msgByGroup($members, array("list"), 200);
    }
}