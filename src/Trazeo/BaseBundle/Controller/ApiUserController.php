<?php

namespace Trazeo\BaseBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sopinet\GCMBundle\Entity\Device;
use Sopinet\GCMBundle\Entity\DeviceRepository;
use Sopinet\UserPreferencesBundle\Entity\UserSetting;
use Sopinet\UserPreferencesBundle\Entity\UserValue;
use Sopinet\UserPreferencesBundle\Entity\UserValueRepository;
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
use Swift_Message as Message;
use Hip\MandrillBundle\Dispatcher;
use Trazeo\BaseBundle\Entity\UserExtend;
use Trazeo\BaseBundle\Service\MailerHelper;

class ApiUserController extends Controller
{

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

        $user = $this->checkUser($request->get('email'), $request->get('pass'));

        return $user;
    }

    /**
     * @POST("/api/login", name="api_login")
     */
    public function postLoginAction(Request $request)
    {
        //Se usan anotaciones para comprobar si el método es post
        //if ('POST' == $request->getMethod() || true) {
        $user = $this->checkPrivateAccess($request);

        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }

        $array['id'] = $user->getId();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK($array));

        return $this->get('fos_rest.view_handler')->handle($view);
        //}else
        //return $this->msgDenied();
    }


    /**
     * @POST("/api/register")
     * @param Request request
     */
    public function postRegisterAction(Request $request)
    {
        //recabamos los datos de la peticion
        $username = $request->get('username');
        $password = $request->get('password');
        //se comprueba si el usuario existe
        $em = $this->get('doctrine.orm.entity_manager');
        $userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByNick($username);
        if ($userextend != null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied("Email is already in use"));

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        //se crea el usuario
        $userManager = $this->container->get('fos_user.user_manager');
        $newUser = $userManager->createUser();
        $newUser->setUsername($username);
        $newUser->setUsernameCanonical($username);
        $newUser->setPassword($password);
        $newUser->setEmail($username);
        $newUser->setEnabled(true);
        $em->persist($newUser);
        $em->flush();

        //$dispatcher = $this->get('hip_mandrill.dispatcher');
        /** @var MailerHelper $mailer */
        $mailer=$this->get('trazeo_mailer_helper');
        $message = $mailer->createNewMessage('hola@trazeo.es', 'Trazeo', $newUser->getEmail(), "Bienvenido a Trazeo.", $this->get('templating')->render('SopinetTemplateSbadmin2Bundle:Emails:newUserApp.html.twig', array()));
        $mailer->sendMessage($message);
        //se devuelve el id del usuario
        $array['id'] = $newUser->getId();
        $view = View::create()
            ->setStatusCode(201)
            ->setData($this->doOK($array));
        return $this->get('fos_rest.view_handler')->handle($view);
    }


    /**
     * @POST("/api/user/points", name="api_user_points")
     */
    public function postUserPointsAction(Request $request)
    {
        $user = $this->checkPrivateAccess($request);

        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }

        $repositoryUserExtend=$this->get('doctrine.orm.default_entity_manager')->getRepository('TrazeoBaseBundle:UserExtend');
        $userextend = $repositoryUserExtend->findOneByNick($user->getEmail());
        $array['points'] = $repositoryUserExtend->getCurrentPoints($userextend);

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK($array));

        return $this->get('fos_rest.view_handler')->handle($view);
    }


    /**
     * @POST("/api/user/profile", name="api_user_profile")
     */
    public function postUserProfileAction(Request $request)
    {
        $user = $this->checkPrivateAccess($request);

        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }

        $repositoryUserExtend=$this->get('doctrine.orm.default_entity_manager')->getRepository('TrazeoBaseBundle:UserExtend');
        $userextend = $repositoryUserExtend->findOneByNick($user->getEmail());

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK($userextend));

        return $this->get('fos_rest.view_handler')->handle($view);
    }




    /**
     * @POST("/api/user/modify/profile", name="api_user_modify_profile")
     */
    public function postUserModifyProfileAction(Request $request)
    {
        $user = $this->checkPrivateAccess($request);

        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }

        $repositoryUserExtend=$this->get('doctrine.orm.default_entity_manager')->getRepository('TrazeoBaseBundle:UserExtend');
        /** @var UserExtend $userextend */
        $userextend = $repositoryUserExtend->findOneByNick($user->getEmail());
        //Obtenemos los datos de la petición
        $name=$request->get('name');
        $phone=$request->get('phone');
        $city=$request->get('city');
        $useLike=$request->get('useLike');
        $city=$this->get('doctrine.orm.default_entity_manager')->getRepository("JJsGeonamesBundle:City")->findOneByNameUtf8($city);
        if($city==null){
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied('City '.$city.' not found'));

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        //Actualizamos los datos del perfil
        $userextend->setName($name);
        $userextend->setCity($city);
        $userextend->setMobile($phone);
        $userextend->setUseLike($useLike);
        //Guardamos los datos
        $this->get('doctrine.orm.default_entity_manager')->persist($userextend);
        $this->get('doctrine.orm.default_entity_manager')->flush();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK('ok'));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * Petición para obtener las preferencías con respecto a las notificaciones de un usuario
     *
     * @ApiDoc(
     *   description="Función que registra un nuevo device para un usuario",
     *   section="user",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario administrador"},
     *      {"name"="pass", "dataType"="string", "required"=true, "description"="Password del usuario administrador"}
     *   }
     * )
     * @POST("/api/user/notification/settings", name="api_user_notification_settings")
     */
    public function postUserNotificationSettingsAction(Request $request)
    {
        $user = $this->checkPrivateAccess($request);

        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        /** @var EntityManager $em */
        $em=$this->get('doctrine.orm.default_entity_manager');

        $repositoryUserExtend=$em->getRepository('SopinetUserBundle:SopinetUserExtend');
        $userextend = $repositoryUserExtend->findOneByUser($user);

        $repositoryUserValue = $em->getRepository('SopinetUserPreferencesBundle:UserValue');

        /** @var UserValue[] $settings */
        $settings=$repositoryUserValue->findByUser($userextend);

        if (count($settings)>0) {
            $data=$settings;
        } else {
            //Si el usuario no tiene settings se crean los settings por defecto
            $settingsRepository=$em->getRepository('SopinetUserPreferencesBundle:UserSetting');
            $settings=$settingsRepository->findAll();
            /** @var UserSetting $setting */
            foreach ($settings as $setting) {
                //Se añade el valor por defecto asociado al usuario y al setting
                $userSetting=new UserValue();
                $userSetting->setUser($userextend);
                $userSetting->setValue($setting->getDefaultoption());
                $userSetting->setSetting($setting);
                $em->persist($userSetting);
                $em->flush($userSetting);
                //Se añaden los datos para la respuesta
                $data[]=$userSetting;
            }
        }
        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK($data));

        return $this->get('fos_rest.view_handler')->handle($view);
    }


    /**
     * @param Request $request
     *
     * @return Response
     * Petición para modificar las preferencías con respecto a las notificaciones de un usuario
     * @POST("/api/user/notification/modify/settings", name="api_user__modify_notification_settings")
     */
    public function postUserModifyNotificationSettingsAction(Request $request)
    {
        $user = $this->checkPrivateAccess($request);

        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $em=$this->get('doctrine.orm.default_entity_manager');

        $repositoryUserExtend=$em->getRepository('SopinetUserBundle:SopinetUserExtend');
        $userextend = $repositoryUserExtend->findOneByUser($user);

        $repositoryUserValue = $em->getRepository('SopinetUserPreferencesBundle:UserValue');
        $repositoryUserValue->setValue($userextend, $request->get('email_notification_id'), $request->get('email_notification_value'));
        $repositoryUserValue->setValue($userextend, $request->get('civiclub_conexion_id'), $request->get('civiclub_conexion_value'));

        /** @var UserValue[] $settings */
        $settings=$repositoryUserValue->findByUser($userextend);

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK('ok'));

        return $this->get('fos_rest.view_handler')->handle($view);
    }


    /**
     * @param Request $request
     *
     * @return Response
     * @POST("/api/user/change/password")
     */
    public function postChangePasswordAction(Request $request)
    {
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $em=$this->get('doctrine.orm.default_entity_manager');
        $password=$request->get('newPassword');
        //se crea el usuario
        $user->setPassword($password);
        $em->persist($user);
        $em->flush();

        $view = View::create()
            ->setStatusCode(201)
            ->setData($this->doOK('ok'));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @ApiDoc(
     *   description="Función que registra un nuevo device para un usuario",
     *   section="user",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario administrador"},
     *      {"name"="pass", "dataType"="string", "required"=true, "description"="Password del usuario administrador"},
     *      {"name"="device_token", "dataType"="string", "required"=true, "description"="Token a registrar"},
     *      {"name"="device", "dataType"="string", "required"=true, "description"="Tipo de dispositivo=iOS|Android"},
     *      {"name"="device_id", "dataType"="string", "required"=true, "description"="Código identificador unico del device"}
     *   }
     * )
     *
     * @POST("/api/user/register/device")

     */
    public function postRegisterDeviceAction(Request $request)
    {
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
        $token=$request->get('device_token');
        $type=$request->get('device');
        //se registra el device
        /** @var DeviceRepository $repositoryDevice */
        $repositoryDevice = $em->getRepository('SopinetGCMBundle:Device');
        if ($request->get('device_id')) {
            $deviceId=$request->get('device_id');
            $repositoryDevice->addDevice($deviceId, $userextend, $token, $type);
        }

        $view = View::create()
            ->setStatusCode(201)
            ->setData($this->doOK('ok'));

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}
