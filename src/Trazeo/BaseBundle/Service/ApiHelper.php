<?php

namespace Trazeo\BaseBundle\Service;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\HttpFoundation\Request;

class ApiHelper {
    private $em;
    private $viewHandler;


    function __construct(EntityManager $em, ViewHandler $viewHandler) {
        $this->em = $em;
        $this->viewHandler=$viewHandler;
    }

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

        return $this->viewHandler->handle($view);
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

        return $this->viewHandler->handle($view);
    }

    /**
     * Funcion que controla el usuario que envia datos a la API, sin estar logueado, con parámetros email y pass
     */
    private function checkUser($email, $password){

        $user = $this->em->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array ("email"=>$email, "password"=>$password));
        //$user= $this->getDoctrine()->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array ("username"=>$email));
        if ($user == null){
            $user = $this->em->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array ("username"=>$email, "password"=>$password));
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
    }}