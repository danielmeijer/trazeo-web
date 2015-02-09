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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Trazeo\BaseBundle\Entity\ERide;
use Trazeo\BaseBundle\Entity\EGroup;
use Trazeo\BaseBundle\Entity\EGroupInvite;
use Trazeo\BaseBundle\Entity\EGroupAccess;
use Sopinet\Bundle\SimplePointBundle\ORM\Type\SimplePoint;
use Sopinet\TimelineBundle\Entity\Comment;
use Hip\MandrillBundle\Message;
use Hip\MandrillBundle\Dispatcher;

class ApiGroupController extends Controller
{

    /**
     * Funcion para representar un uso erroneo de la API
     */
    private function msgDenied($msg = null)
    {
        $array['state'] = -1;
        if ($msg != null) $array['msg'] = $msg;
        else $array['msg'] = "Access Denied";
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
        } else
            $ret['data'] = $data;
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
     * @POST("/api/deleteGroup")
     * @param Request request
     */
    public function deleteGroupAction(Request $request)
    {

        $id_group = $request->get('id_group');
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $em = $this->get('doctrine.orm.entity_manager');
        $userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
        //Obtenemos el repositorio del grupo
        $repositoryGroup = $em->getRepository('TrazeoBaseBundle:EGroup');
        //Intentamos borrar el grupo
        try {
            $repositoryGroup->userDeleteGroup($id_group, $userextend);
            //Grupo borrado con exito
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->doOK('ok'));
            return $this->get('fos_rest.view_handler')->handle($view);
        } //no encontramos el grupo
        catch (PreconditionFailedHttpException $e) {
            return $this->exceptionHandler($e);
        } //el usuario no es el administrador del grupo
        catch (AccessDeniedException $e) {
            return $this->exceptionHandler($e);
        }
    }


    /**
     * @POST("/api/groups")
     * @param Request request
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
            if ($group->getCity()) $arrayGroups['city'] = $group->getCity()->getNameUtf8();
            $arrayGroups['school'] = $group->getSchool1();
            if (in_array($group, $admingroups)) $arrayGroups['admin'] = true;
            else $arrayGroups['admin'] = false;
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
     * Petición para crear un grupo en la bbdd
     * @POST("/api/manageGroup")
     */
    public function manageGroupAction(Request $request)
    {

        $name = $request->get('name');
        $visibility = $request->get('visibility');
        $id_group = $request->get('id_group');
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
        if ($group != null && $group != $em->getRepository('TrazeoBaseBundle:EGroup')->find($id_group)) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied("Name is already in use"));

            return $this->get('fos_rest.view_handler')->handle($view);
        }

        //Si el grupo existe se modifica si no se crea uno nuevo
        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneBy(array('id' => $id_group, 'admin' => $userextend));

        //Si el usuario no es el admin del grupo y lo intenta modificar se deniega el ascesso
        if ($id_group != null && $group == null) {
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
            $city_entity = $helper->getCities($city, 10, true);
            if (count($city_entity) > 0) {
                $group->setCity($city_entity[0]);
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
                    if (in_array($group, $admingroups)) $arrayGroups['admin'] = true;
                    else $arrayGroups['admin'] = false;
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
            if ($group != null) $aux[] = $group;
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
                if (in_array($group, $admingroups)) $arrayGroups['admin'] = true;
                else $arrayGroups['admin'] = false;
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
     * @POST("/api/joinGroup")
     */
    public function joinGroupAction(Request $request)
    {
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $id_group = $request->get('id_group');

        $em = $this->getDoctrine()->getManager();
        $userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($id_group);

        if (!$group) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied("Group doesn't exist"));

            return $this->get('fos_rest.view_handler')->handle($view);
        }

        $userextends = $group->getUserextendgroups()->toArray();

        if (in_array($userextend, $userextends)) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied("User it's already on group"));
            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $groupAdmin = $group->getAdmin();
        $groupVisibility = $group->getVisibility();
        $info = array();
        if ($groupAdmin == $user || $groupVisibility == 0) {

            $group->addUserextendgroup($userextend);
            $em->persist($group);
            $em->flush();

            //Children autojoin on parent join to group
            $childs = $userextend->getChilds();
            foreach ($childs as $child) {
                $group->addChild($child);
            }
            $em->persist($group);
            $em->flush();

            $info['joined'] = 'true';
            $response = json_encode($info);
            return new Response($response, 200, array(
                'Content-Type' => 'application/json'
            ));
        }
        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->msgDenied("The group is not public"));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
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
        $id_group = $request->get('id_group');
        $reGroup = $em->getRepository('TrazeoBaseBundle:EGroup');

        $reGroup->disjoinGroup($id_group, $userextend);

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK('ok'));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
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
        $id_group = $request->get('id_group');
        $id_child = $request->get('id_child');
        $add = $request->get('add');

        $reGroup = $em->getRepository('TrazeoBaseBundle:EGroup');
        $reGroup->setChildOnGroup($id_group, $id_child, $userextend, $add);

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK('ok'));

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}