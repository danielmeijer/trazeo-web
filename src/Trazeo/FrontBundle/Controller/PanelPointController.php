<?php

namespace Trazeo\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
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
use Trazeo\BaseBundle\Entity\File;
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
        $citys=[];
        foreach ($oferts as $ofert) {
            if(count($ofert->getFile()->getValues())>0){
                $files[]=$ofert->getFile()->getValues()[0];
            } else {
                $files[]=new File();
            }
            if($ofert->getCitys()!=null && !in_array($ofert->getCitys(),$citys)) {
                $citys[]=$ofert->getCitys();
            }

        }

        return array(
            'user' => $user,
            'city' => $user->getCity(),
            'points' => $points,
            'oferts' => $oferts,
            'files' => $files,
            'exchange'=> $exchange,
            'cities'=>$citys
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
        $citys=[];
        foreach($oferts as $ofert){
            if(!in_array($ofert->getCitys(),$citys))$citys[]=$ofert->getCitys();
        }

        $gamification = $this->container->get('sopinet_gamification');
        $points=$gamification->getUserPoints();
        foreach ($oferts as $ofert) {
            $files[]=$ofert->getFile()->getValues()[0];
        }
        return array(
            'user' => $user,
            'city' => $user->getCity(),
            'points' => $points,
            'oferts' => $oferts,
            'files' => $files,
            'exchange'=> $exchange,
            'cities'=> $citys
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
        /** @var Translator $translator */
        $translator = $this->get('translator');
        $fos_user = $this->container->get('security.context')->getToken()->getUser(); 
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        $container = $this->get('sopinet_flashMessages');  
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        $ofert = $em->getRepository('TrazeoBaseBundle:ECatalogItem')->find($id);

        if(($user->getPoints()-$user->getSpendedPoints())<$ofert->getPoints()){
         $notification = $container->addFlashMessages("success", $translator->trans('flash_messages.not_points_needed'));
         return $this->redirect($this->generateUrl('panel_point', array('exchange' => 2)));
        }
        $user->setSpendedPoints($user->getSpendedPoints()+$ofert->getPoints());
        $em->persist($user);
        $em->flush();

        $body = $translator->trans('Request2', array('%nick%' => $user->getNick(), '%title%' => $ofert->getTitle(), '%company%' => $ofert->getCompany()));

        $message = \Swift_Message::newInstance()
        ->setFrom(array("hola@trazeo.es" => "Trazeo"))
        ->setTo("hola@trazeo.es")
        ->setSubject($translator->trans('Request'))
        ->setBody('<p>'.$body.'</p>', 'text/html');
        $ok = $this->container->get('mailer')->send($message);

        $notification = $container->addFlashMessages("success", $translator->trans('flash_messages.request_send'));
        return $this->redirect($this->generateUrl('panel_point',array('exchange' => 1)));
    }
}