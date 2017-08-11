<?php

namespace Trazeo\FrontBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use FOS\RestBundle\Controller\FOSRestController;
use Swift_Message as Message;;
use Hip\MandrillBundle\Dispatcher;

/**
 * @Route("/panel")
 */
class PanelController extends Controller
{
    /**
     * @Route("/", name="panel_dashboard")
     * @Template()
     */
    public function homeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fos_user = $this->container->get('security.context')->getToken()->getUser();
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);

        $userId = $user->getId();
        $not = $em->getRepository('SopinetUserNotificationsBundle:Notification')->findByUser($userId);

        $childs = $user->getChilds();
        $groupsAdmin = $user->getAdminGroups();
        $routes = $user->getAdminRoutes();

        $groupsMember = $user->getGroups();

        $allGroups = $em->getRepository('TrazeoBaseBundle:EGroup')->findAll();
        $restGroups = array_diff($allGroups,$groupsMember->toArray());
        //ldd($restGroups);
        $groupsRide = array();

        foreach($groupsMember as $groupMember){
            if($groupMember->getHasRide() == 1){
                $groupsRide[] = $groupMember;
            }
        }
        $tutorial = 0;
        if(!$user->getTutorial()){
            $user->setTutorial(1);
            $em->persist($user);
            $em->flush();
            $tutorial = 1;
            // Creamos el correo de bienvenida
            $mailer = $this->get('trazeo_mailer_helper');
            /** @var Translator $translator */
            $translator = $this->get('translator');
            $message = $mailer->createNewMessage('hola@trazeo.es', 'Trazeo', $fos_user->getEmail(), $translator->trans('home_welcome_mail'), $this->get('templating')->render('SopinetTemplateSbadmin2Bundle:Emails:newUser.html.twig', array()));
            $mailer->sendMessage($message);

        }
        /**
         * Do Suggestion
         */
        $reSu = $em->getRepository('SopinetSuggestionBundle:ESuggestion');
        $sugs = $reSu->getSuggestionsFor($user->getUseLike(), 'home');
        $suggestion=null;

        foreach($sugs as $sug) {
            if (eval($sug->getRule())) {
                $suggestion = $sug;
                break;
            }
        }

        if($suggestion!=null)$suggestion->setText($this->get('translator')->trans('Suggestion.'.$suggestion->getText()));
        /** END SUGGESTION **/

        return array(
            'user' => $user,
            'childs' => $childs,
            'groupsAdmin' => $groupsAdmin,
            'routes' => $routes,
            'notifications' => $not,
            'groupsRide' => $groupsRide,
            'tutorial' => $tutorial,
            'restGroups' => $restGroups,
            'groupsMember' => $groupsMember,
            'suggestion' => $suggestion
        );
    }

    /**
     * @Route("/doMonitor", name="panel_doMonitor")
     */
    public function doMonitor() {
        $em = $this->getDoctrine()->getManager();
        $fos_user = $this->container->get('security.context')->getToken()->getUser();
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);

        $user->setUseLike("monitor");
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('panel_dashboard'));
    }

    /**
     * @Route("/doUser", name="panel_doUser")
     */
    public function doUser() {
        $em = $this->getDoctrine()->getManager();
        $fos_user = $this->container->get('security.context')->getToken()->getUser();
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);

        $user->setUseLike("user");
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('panel_child_new'));
    }
    /**
     * Get actual rides
     *
     * Devuelve los paseos en curso para el usuario actual
     *
     * @Route("/getRides", name="panel_getRides")
     * @Method("GET")
     */
    public function getRides()
    {
        $em = $this->getDoctrine()->getManager();
        $fos_user = $this->container->get('security.context')->getToken()->getUser();
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);

        $userId = $user->getId();

        $groupsMember = $user->getGroups();

        $allGroups = $em->getRepository('TrazeoBaseBundle:EGroup')->findAll();
        $restGroups = array_diff($allGroups,$groupsMember->toArray());
        //ldd($restGroups);
        $groupsRide = array();

        foreach($groupsMember as $groupMember){
            if($groupMember->getHasRide() == 1){
                $groupsRide[] = $groupMember;
            }

        }

        $view = view::create()
            ->setFormat('json')
            ->setStatusCode(200)
            ->setData($groupsRide);

        return $this->get('fos_rest.view_handler')->handle($view);

    }
}