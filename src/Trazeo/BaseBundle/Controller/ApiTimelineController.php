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
use Sopinet\TimelineBundle\Entity\Comment;

class ApiTimelineController extends Controller
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
    private function checkUser($email, $password)
    {

        $user = $this->getDoctrine()->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array("email" => $email, "password" => $password));
        //$user= $this->getDoctrine()->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array ("username"=>$email));
        if ($user == null) {
            $user = $this->getDoctrine()->getRepository('\Application\Sonata\UserBundle\Entity\User')->findOneBy(array("username" => $email, "password" => $password));
            if ($user == null) {
                return false;
            }
        }
        if ($password == $user->getPassword()) {
            return $user;
        } else
            return false;
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
     * Crea un nuevo mensaje en el TimeLine (Muro) del Grupo
     *
     * @POST("/api/group/timeline/new")
     * @param Request $request
     */
    public function newTimeLineAction(Request $request)
    {
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $em = $this->get('doctrine.orm.entity_manager');
        $id_group = $request->get('id_group');
        $text = $request->get('text');

        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneById($id_group);
        $thread = $em->getRepository('SopinetTimelineBundle:Thread')->findOneById($id_group);

        // Save comment
        $comment = new Comment();
        $comment->setThread($thread);
        $comment->setAuthor($user);
        $comment->setBody($text);

        $em->persist($comment);
        $em->flush();

        // Send notifications to Users
        $userextends = $group->getUserextendgroups()->toArray();
        $not = $this->container->get('sopinet_user_notification');
        foreach ($userextends as $userextend) {
            $url = $this->get('trazeo_base_helper')->getAutoLoginUrl($user, 'panel_group_timeline', array('id' => $group->getId()));
            $not->addNotification(
                "timeline.newFromMonitor",
                "TrazeoBaseBundle:EGroup,SopinetTimelineBundle:Comment",
                $group->getId() . "," . $comment->getId(),
                $url,
                $userextend->getUser(),
                null,
                $this->generateUrl('panel_group_timeline', array('id' => $group->getId()))
            );
        }

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK($comment));

        return $this->get('fos_rest.view_handler')->handle($view);
    }


    /**
     * Lista los mensajes del TimeLine (Muro) del Grupo
     *
     * @POST("/api/group/timeline/list")
     * @param Request $request
     */
    public function getTimeLineAction(Request $request)
    {
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }

        $em = $this->get('doctrine.orm.entity_manager');

        $id_group = $request->get('id_group');

        $thread = $em->getRepository('SopinetTimelineBundle:Thread')->findOneById($id_group);
        $comments = $em->getRepository('SopinetTimelineBundle:Comment')->findByThread($thread);
        $data = array();
        foreach ($comments as $comment) {
            $comment->setAuthorName($comment->getAuthorName());
            $data[] = $comment;
        }

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK($data));

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}