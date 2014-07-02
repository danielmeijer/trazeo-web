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

        return array(
            'user' => $user,
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
        $userChilds=$user->getChilds();
        $points=[];
        $distances=[];
        foreach ($userChilds as $userChild) {
            $distances[$userChild->getId()]=0;
            $childrides = $em->getRepository("TrazeoBaseBundle:EChildRide")->findByChild($userChild);
            foreach ($childrides as $childride){
                $distances[$userChild->getId()]+=$childride->getDistance();
            }
            $points[$userChild->getId()]=floor($distances[$userChild->getId()]/1000);
        }
        
        return array(
        'userChilds' => $userChilds,
        'distances' => $distances,
        'points' => $points
        );
    }

}