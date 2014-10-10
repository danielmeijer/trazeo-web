<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Trazeo\BaseBundle\Entity\EGroup;
use Trazeo\BaseBundle\Entity\ERoute;
use Trazeo\BaseBundle\Entity\EGroupAccess;
use Trazeo\BaseBundle\Entity\EGroupInvite;
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\BaseBundle\Form\GroupType;
use Trazeo\BaseBundle\Controller\GroupsController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * UserExtend controller.
 *
 * @Route("/panel/point")
 */
class PanelPointController extends Controller
{

    /**
     * Show user point.
     *
     * @Route("/", name="panel_point")
     * @Method("GET")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $fos_user = $this->container->get('security.context')->getToken()->getUser();   
        $exchange=$request->get('exchange');
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        $oferts = $em->getRepository('TrazeoBaseBundle:ECatalogItem')->findBy(
             array('complete'=> '1'), 
             array('position' => 'ASC')
           );
        $gamification = $this->container->get('sopinet_gamification');
        $points=$gamification->getUserPoints();
        foreach ($oferts as $ofert) {
            $files[]=$ofert->getFile()->getValues()[0];
        }
        return array(
            'user' => $user,
            'points' => $points,
            'oferts' => $oferts,
            'files' => $files,
            'exchange'=> $exchange
        );
    }

    /**
     * Show user point.
     *
     * @Route("/exchange/{exchange}", name="panel_point_exchange")
     * @Method("GET")
     * @Template("TrazeoFrontBundle:PanelPoint:index.html.twig")
     */
    public function exchangedAction($exchange)
    {
        $em = $this->getDoctrine()->getManager();
        $fos_user = $this->container->get('security.context')->getToken()->getUser();   
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        $oferts = $em->getRepository('TrazeoBaseBundle:ECatalogItem')->findBy(
             array('complete'=> '1'), 
             array('position' => 'ASC')
           );
        $gamification = $this->container->get('sopinet_gamification');
        $points=$gamification->getUserPoints();
        foreach ($oferts as $ofert) {
            $files[]=$ofert->getFile()->getValues()[0];
        }
        return array(
            'user' => $user,
            'points' => $points,
            'oferts' => $oferts,
            'files' => $files,
            'exchange'=> $exchange
        );
    }
    /**
     * Show user point.
     *
     * @Route("/historical", name="panel_point_historical")
     * @Method("GET")
     * @Template()
     */
    public function historicalAction()
    {
        $em = $this->getDoctrine()->getManager();
        $fos_user = $this->container->get('security.context')->getToken()->getUser();   
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        $gamification = $this->container->get('sopinet_gamification');
        $all_actions=$gamification->getUserActions($user);

        return $all_actions;
    }


    /**
     * Show user point.
     *
     * @Route("/{id}/exchange", name="panel_point_exchange")
     * @Method("GET")
     * @Template()
     */
    public function exchangeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $fos_user = $this->container->get('security.context')->getToken()->getUser(); 
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        $container = $this->get('sopinet_flashMessages');  
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        $ofert = $em->getRepository('TrazeoBaseBundle:ECatalogItem')->find($id);

        if(($user->getPoints()-$user->getSpendedPoints())<$ofert->getPoints()){
         $notification = $container->addFlashMessages("success","Tu solicitud no ha sido enviada ya que no tienes los puntos necesarios");
         return $this->redirect($this->generateUrl('panel_point', array('exchange' => 2)));           
        }
        $user->setSpendedPoints($user->getSpendedPoints()+$ofert->getPoints());
        $em->persist($user);
        $em->flush();

        $message = \Swift_Message::newInstance()
        ->setFrom(array("hola@trazeo.es" => "Trazeo"))
        ->setTo("hola@trazeo.es")
        ->setSubject('Solicitud de canjeo de usuario')
        ->setBody('<p>Solicitud de canjeo del usuario '.$user->getNick().' para la oferta '.$ofert->getTitle().' de la empresa '.$ofert->getCompany(). '</p>', 'text/html');
        $ok = $this->container->get('mailer')->send($message);

        $notification = $container->addFlashMessages("success","Tu solicitud ha sido enviada y se está procesando");
        return $this->redirect($this->generateUrl('panel_point',array('exchange' => 1)));
    }
}