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
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $fos_user = $this->container->get('security.context')->getToken()->getUser();   
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        $gamification = $this->container->get('sopinet_gamification');
        $points=$gamification->getUserPoints();
        return array(
            'user' => $user,
            'points' => $points
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
        $actions=$gamification->getUserActions($user);
                
        return $actions;
    }


    /**
     * Show user point.
     *
     * @Route("/exchange", name="panel_point_exchange")
     * @Method("GET")
     * @Template()
     */
    public function exchangeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $fos_user = $this->container->get('security.context')->getToken()->getUser();   
        $user = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($fos_user);
        $gamification = $this->container->get('sopinet_gamification');
        $actions=$gamification->getUserActions($user);
                
        return $actions;
    }

}