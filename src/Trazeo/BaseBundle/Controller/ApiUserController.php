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
use Hip\MandrillBundle\Message;
use Hip\MandrillBundle\Dispatcher;

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

        $dispatcher = $this->get('hip_mandrill.dispatcher');

        $message = new Message();

        $message
            ->setFromEmail('hola@trazeo.es')
            ->setFromName('Trazeo')
            ->addTo($newUser->getEmail())
            ->setSubject("Bienvenido a Trazeo.")
            ->setHtml($this->get('templating')->render('SopinetTemplateSbadmin2Bundle:Emails:newUserApp.html.twig', array()));


        $result = $dispatcher->send($message);
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
        $userextend = $repositoryUserExtend->findOneByNick($user->getUsername());
        $array['points'] = $repositoryUserExtend->getCurrentPoints($userextend);

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK($array));

        return $this->get('fos_rest.view_handler')->handle($view);
        //}else
        //return $this->msgDenied();
    }

}
