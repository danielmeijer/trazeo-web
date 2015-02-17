<?php

namespace Trazeo\MyPageBundle\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sonata\AdminBundle\Admin\AdminInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\BaseBundle\Entity\EChildRepository;
use Trazeo\BaseBundle\Entity\EGroup;
use Trazeo\BaseBundle\Entity\EGroupRepository;
use Trazeo\BaseBundle\Entity\ERide;
use Trazeo\BaseBundle\Entity\ERideRepository;
use Trazeo\MyPageBundle\Form\BarAdminType;
use Trazeo\MyPageBundle\Form\RegisteredAdminType;
use Trazeo\MyPageBundle\Form\RegisteredEvolutionAdminType;

/**
 * @Route("/admin/ride")
 */
class RideAdminController extends Controller
{
    /**
     * @Route("/list/", name="ride_list")
     * @Template()
     */
    public function listAction(Request $request)
    {
        // http://blog.eike.se/2014/03/custom-page-controller-in-sonata-admin.html
        $admin_pool = $this->get('sonata.admin.pool');


    }
}