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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Trazeo\BaseBundle\Entity\EChild;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ApiChildController extends Controller
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
     * @POST("/api/manageChild")
     * @param Request request
     */
    public function manageChildrenAction(Request $request)
    {

        $id_child = $request->get('id_child');
        $name = $request->get('name');
        $school = $request->get('school');
        $date = $request->get('date');
        $gender = $request->get('gender');//boy girl

        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $em = $this->get('doctrine.orm.entity_manager');
        $userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
        //Si el niño existe se modifica si no se crea uno nuevo
        $child = $em->getRepository('TrazeoBaseBundle:EChild')->findOneById($id_child);
        $tutor = in_array($child, $userextend->getChilds()->toArray());

        $new = false;//flag que indica si el niño se va a crear como nuevo
        //Si el usuario no es el tutor del niño y lo intenta modificar se deniega el ascesso
        if ($id_child != null && $tutor == false) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied("User is not the tutor"));

            return $this->get('fos_rest.view_handler')->handle($view);
        } //Si el niño no existe se crea
        else if ($child == null) {
            $child = new EChild();
            $new = true;
        }

        if ($date) $child->setDateBirth(new \DateTime($date));
        else $child->setDateBirth(new \DateTime());
        if ($school) $child->setScholl($school);
        if ($new) $child->addUserextendchild($userextend);
        $child->setSelected(false);
        $child->setGender($gender);
        $child->setNick($name);
        $child->setVisibility(true);

        $em->persist($child);
        $em->flush();

        if ($new) {
            $userextend->addChild($child);
            $em->persist($userextend);
            $em->flush();
        }
        //Se devuelve el id del niño
        $data['id'] = $child->getId();
        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK($data));
        return $this->get('fos_rest.view_handler')->handle($view);
    }


    /**
     * @POST("/api/deleteChild")
     * @param Request request
     */
    public function deleteChildrenAction(Request $request)
    {
        //Comprobamos el acceso del usuario al sistema
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }
        $em = $this->get('doctrine.orm.entity_manager');
        $userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);
        $repositoryChild = $em->getRepository('TrazeoBaseBundle:EChild');
        $id_child = $request->get('id_child');
        //Tratamos de borrar el niño
        try {
            $repositoryChild->userDeleteChild($id_child, $userextend);
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->doOK('ok'));
            return $this->get('fos_rest.view_handler')->handle($view);
        } //si no encontramos el niño
        catch (NotFoundHttpException $e) {
            return $this->exceptionHandler($e);

        } //Si el usuario no es el tutor del niño y lo intenta borrar
        catch (AccessDeniedException $e) {
            return $this->exceptionHandler($e);
        }
    }

    /**
     * @POST("/api/user/childs")
     */
    public function getUserChildrensAction(Request $request)
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

        $childs = $userextend->getChilds();

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK($childs));

        return $this->get('fos_rest.view_handler')->handle($view);
    }

    /**
     * @param Request $request
     *
     * @return View|Response
     * @ApiDoc(
     *   description="Función que devuelve el listado medallas",
     *   section="child",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="pass", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *   }
     * )
     *
     * @POST("/api/user/childrenMedals")
     */
    public function getUserChildrenMedalsAction(Request $request) {
        $user = $this->checkPrivateAccess($request);
        if ($user == false || $user == null) {
            $view = View::create()
                ->setStatusCode(200)
                ->setData($this->msgDenied());

            return $this->get('fos_rest.view_handler')->handle($view);
        }

        $em = $this->get('doctrine.orm.entity_manager');

        $userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);

        $childs = $userextend->getChilds();

        $childrenOk = array();
        /** @var EChild $child */
        foreach($childs as $child) {
            $childOk = array();
            $childOk['id'] = $child->getId();
            $childOk['name'] = $child->getNick();
            $childOk['medals'] = $child->getMedals();
            $childrenOk[] = $childOk;
        }

        $view = View::create()
            ->setStatusCode(200)
            ->setData($this->doOK($childrenOk));

        return $this->get('fos_rest.view_handler')->handle($view);
    }
}