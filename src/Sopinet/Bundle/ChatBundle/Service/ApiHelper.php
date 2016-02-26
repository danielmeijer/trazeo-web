<?php

namespace Sopinet\Bundle\ChatBundle\Service;
use FOS\RestBundle\View\ViewHandler;
use Sopinet\Bundle\ChatBundle\Entity\Chat;
use  Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\SerializationContext;
use Trazeo\BaseBundle\Entity\UserExtend;
/**
 * Created by PhpStorm.
 * User: sopinet
 * Date: 11/11/14
 * Time: 8:41 AM
 */

class ApiHelper {

    const OK = "ok";
    const DENIED= "Data not valid";
    const USERNOTVALID = "User starter not valid";
    const USERNOTINCHAT= "User is member of the chat";
    const USERSTARTERNOTVALID = "User starter not valid";
    const USERNOTADMIN = "User not admin";
    const CHATTYPEINCORRECT = "The type of chat must be event";
    const NOTMONEY = "User dont have enough money";
    const GENERALERROR = "General error";
    const NODEVICE= "User don't have device registered";

    public function __construct(EntityManager $entityManager, ViewHandler $viewHandler) {
        $this->em = $entityManager;
        $this->viewhandler=$viewHandler;
    }

    /**
     * Funcion para representar un uso erroneo de la API
     * @param String $msg mensaje
     * @return Array $array mensaje con el estado
     */
    private function doDenied($msg=null) {
        $array['state'] = -1;
        if($msg!=null)$array['msg'] = $msg;
        else $array['msg'] = "Access Denied";
        return $array;
    }

    /**
     * @return mixed
     */
    public function msgOk($msg=null, $status=200) {
        $view = view::create()
            ->setStatusCode($status)
            ->setData($this->doOk($msg));
        return $this->viewhandler->handle($view);
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
     * @return mixed
     */
    public function msgDenied($msg=null, $status=200) {
        $view = view::create()
            ->setStatusCode($status)
            ->setData($this->doDenied($msg));

        return $this->viewhandler->handle($view);
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
     *
     * @param $email
     * @param $password
     * @return bool
     */
    private function checkUser($email, $password){

        $user = $this->em->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array ("email"=>$email, "password"=>$password, "enabled"=>1));
        //$user= $this->getDoctrine()->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array ("username"=>$email));
        if ($user == null){
            $user = $this->em->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array ("email"=>$email, "username"=>$password, "enabled"=>1));
        }
        if ($user ==null) {
            return false;
        }
        return $user;
    }


    /**
     * Funcion que controla si el usuario está logueado o se comprueba con su email y pass
     * @param String email
     * @param String password
     * @return bool
     */
    public  function checkPrivateAccess(Request $request) {
         $user = $this->checkUser($request->get('email'), $request->get('password'));
        //No es necesario
        if($user == false) {
            return false;
        }

        return $user;
    }

    /**
     * Función que comprueba si el usuario está logueado o tiene un email y pass válido
     * También comprueba que está en el chat que se pasa y es Administrador del mismo
     *
     * @param Request $request
     * @param Chat $chat
     * @return bool
     */
    public function checkAdminAccess(Request $request, Chat $chat) {
        $user = $this->checkUser($request->get('email'), $request->get('password'));

        if($user == false) {
            return false;
        }
        if($chat->getAdmin()->getUser()/*FIXME*/==$user)return $user;
        return false;
    }

    public function dumpVar($var){
        return $var;
    }


    /**
     * Funcion para manejar una excepcion
     * @param exception
     * @return view
     */
    public function exceptionHandler($e) {
        $view = View::create()
            ->setStatusCode(400)
            ->setData($this->msgDenied($e->getMessage()));

        return $this->viewhandler->handle($view);
    }
} 