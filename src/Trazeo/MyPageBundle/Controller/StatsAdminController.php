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
 * @Route("/admin/stats")
 */
class StatsAdminController extends Controller
{
    /**
     * @Route("/bar/", name="stats_bar")
     * @Template()
     */
    public function barAction(Request $request) {
        // http://blog.eike.se/2014/03/custom-page-controller-in-sonata-admin.html
        $admin_pool = $this->get('sonata.admin.pool');

        $form_filters = $this->createForm(new BarAdminType($this));

        $obEdad = null;
        $obSexo = null;

        if ($request->getMethod() == "POST") {
            $data = $request->get('BarAdmin');

            /** @var EChildRepository $repositoryEChild */
            $repositoryEChild = $this->getDoctrine()->getRepository('TrazeoBaseBundle:EChild');
            /** @var QueryBuilder $qb */
            $qb = $repositoryEChild->createQueryBuilder("c");

            // Filtro por Grupos
            $group_ids = $this->getGroupsIDs($data);
            //$qb->where('c.groups IN (:group_ids)');
            $qb->leftJoin('c.groups','g');
            $qb->where('g.id IN (:group_ids)');
            $qb->setParameter('group_ids', $group_ids);

            // Filtro por Fecha
            if (isset($data['date_from']) && $data['date_from'] != "") {
                $date_temp_formated = new \DateTime($data['date_from']);
                $qb->andWhere('c.createdAt > :date_from');
                $qb->setParameter('date_from', $date_temp_formated->format('Y-m-d'));
            }

            if (isset($data['date_to']) && $data['date_to'] != "") {
                $date_temp_formated = new \DateTime($data['date_to']);
                $qb->andWhere('c.createdAt < :date_to');
                $qb->setParameter('date_to', $date_temp_formated->format('Y-m-d'));
            }

            $childs = $qb->getQuery()->getResult();

            // Calculamos Datos
            $years = array();
            $boys = 0;
            /** @var EChild $child */
            foreach($childs as $child) {
                if ($child->getGender() == EChild::GENDER_BOY) {
                    $boys++;
                }
                if (!isset($years[$child->getYears()])) $years[$child->getYears()] = 0;
                $years[$child->getYears()]++;
            }
            $girls = count($childs) - $boys;

            ksort($years);
            $data_years = array();
            foreach($years as $key => $value) {
                $data_temp = array((string) $key . " Años", $value);
                $data_years[] = $data_temp;
            }

           // ldd($data_years);
            $data = array(
                array('Ch', 19),
                    array('Gi', 12)
            );

            // Gráfico EDAD
            $obEdad = new Highchart();
            $obEdad->chart->renderTo('edad');
            $obEdad->title->text('Gráfico por Edad');
            $obEdad->plotOptions->pie(array(
                'allowPointSelect'  => true,
                'cursor'    => 'pointer',
                'dataLabels'    => array('enabled' => false),
                'showInLegend'  => true
            ));
            $obEdad->series(array(array('type' => 'column', 'name' => 'Nº niños/as', 'data' => $data_years)));

            // Gráfico SEXO
            $obSexo = new Highchart();
            $obSexo->chart->renderTo('sexo');
            $obSexo->title->text('Gráfico por Sexo');
            $obSexo->plotOptions->pie(array(
                'allowPointSelect'  => true,
                'cursor'    => 'pointer',
                'dataLabels'    => array('enabled' => false),
                'showInLegend'  => true
            ));
            $data_gender = array(
                array('Niños', $boys),
                array('Niñas', $girls)
            );
            $obSexo->series(array(array('type' => 'pie', 'sexo' => 'Nº niños/as', 'data' => $data_gender)));

            //ldd($childs);

            //ldd($data);
            /**
            $ob = new Highchart();
            $ob->chart->renderTo('piechart');
            $ob->title->text('Browser market shares at a specific website in 2010');
            $ob->plotOptions->pie(array(
                'allowPointSelect'  => true,
                'cursor'    => 'pointer',
                'dataLabels'    => array('enabled' => false),
                'showInLegend'  => true
            ));
            $data = array(
                array('Firefox', 45.0),
                array('IE', 26.8),
                array('Chrome', 12.8),
                array('Safari', 8.5),
                array('Opera', 6.2),
                array('Others', 0.7),
            );
            $ob->series(array(array('type' => 'pie','name' => 'Browser share', 'data' => $data)));
             **/
        }

        return array(
            'admin_pool' => $admin_pool,
            'form_filters' => $form_filters->createView(),
            'chartEdad' => $obEdad,
            'chartSexo' => $obSexo
        );
    }

    /**
     * @Route("/registered_evolution/", name="stats_registered_evolution")
     * @Template()
     */
    public function registeredevolutionAction(Request $request)
    {
        // http://blog.eike.se/2014/03/custom-page-controller-in-sonata-admin.html
        $admin_pool = $this->get('sonata.admin.pool');

        //$child = new EChild();
        $form_filters = $this->createForm(new RegisteredEvolutionAdminType($this));

        $ob = null;

        if ($request->getMethod() == "POST") {
            $data = $request->get('RegisteredEvolutionAdmin');

            /** @var ERideRepository $repositoryERide */
            $repositoryERide = $this->getDoctrine()->getRepository('TrazeoBaseBundle:ERide');
            /** @var QueryBuilder $qb */
            $qb = $repositoryERide->createQueryBuilder("r");

            // Filtro por Grupos
            $group_ids = $this->getGroupsIDs($data);
            $qb->where('r.groupid IN (:group_ids)');
            $qb->setParameter('group_ids', $group_ids);

            // Filtro por Fecha
            if (isset($data['date_from']) && $data['date_from'] != "") {
                $date_temp_formated = new \DateTime($data['date_from']);
                $qb->andWhere('r.createdAt > :date_from');
                $qb->setParameter('date_from', $date_temp_formated->format('Y-m-d'));
            }

            if (isset($data['date_to']) && $data['date_to'] != "") {
                $date_temp_formated = new \DateTime($data['date_to']);
                $qb->andWhere('r.createdAt < :date_to');
                $qb->setParameter('date_to', $date_temp_formated->format('Y-m-d'));
            }

            $return = array();

            //TODO: DEBUG, todos los paseos - $qb = $repositoryERide->createQueryBuilder("r");
            $rides = $qb->getQuery()->getResult();

            /** @var ERide $ride */
            foreach($rides as $ride) {
                //ld($ride->getId());
                $children = $repositoryERide->getChildrenInRide($ride);
                if ($children != null) {

                    if (!isset($return[$ride->getGroupid()])) $return[$ride->getGroupid()] = array();
                    if (!isset($return[$ride->getGroupid()][$ride->getCreatedAt()->format('Y-m-d')])) {
                        $return[$ride->getGroupid()][$ride->getCreatedAt()->format('Y-m-d')] = array();
                        $return[$ride->getGroupid()][$ride->getCreatedAt()->format('Y-m-d')]['total'] = 0;
                        $return[$ride->getGroupid()][$ride->getCreatedAt()->format('Y-m-d')]['ride'] = $ride;
                    }

                    $return[$ride->getGroupid()][$ride->getCreatedAt()->format('Y-m-d')]['total'] += count($children);
                }
            }

            // Pintamos las gráfica (una por grupo)
            foreach($return as $key => $value) {
                // Chart
                $formattedData = $this->filterByModeDatePlus($value);

                $series = array(
                    array("name" => "Grupo1",    "data" => $formattedData['data']),
                );

                $new_chart = new Highchart();
                $new_chart->chart->renderTo('linechart'.$key);  // The #id of the div where to render the chart
                $new_chart->title->text('Gráfica');
                $new_chart->xAxis->title(array('text'  => "Fecha"));
                $new_chart->xAxis->categories($formattedData['label']);
                $new_chart->yAxis->title(array('text'  => "Número de Registros"));
                $new_chart->series($series);

                $ob[] = $new_chart;
            }


            //ldd($return);
        }

        return array(
            'admin_pool' => $admin_pool,
            'form_filters' => $form_filters->createView(),
            'charts' => $ob
        );
    }

    private function getGroupsIDs($data) {
        // Filtro por Grupos
        if (isset($data['group'])) {
            $group_ids = $data['group'];
        } else {
            /** @var Helper $helper */
            $helper = $this->container->get('trazeo_base_helper');
            $page = $helper->getPageBySubdomain("torrelodones"); // TODO: Sólo para pruebas, cambiar
            $repositoryEGroup = $this->getDoctrine()->getRepository('TrazeoBaseBundle:EGroup');
            /** @var QueryBuilder $qbTemp */
            $qbTemp = $repositoryEGroup->createQueryBuilder('g')
                ->where('g.page = :page')
                ->setParameter('page', $page)
                ->orderBy('g.name', 'ASC');
            $groups = $qbTemp->getQuery()->getArrayResult();
            /** @var EGroup $g */
            foreach($groups as $g) {
                $group_ids[] = $g['id'];
            }
        }

        return $group_ids;
    }

    private function filterByModeDatePlus($data, $modeDate = "month") {
        switch ($modeDate) {
            case "day":
                $formatCode = "Y-m-d";
                $formatView = "d-m-Y";
                break;

            case "week":
                $formatCode = "Y-W";
                $formatView = "l, d-m-Y";
                break;

            case "month":
                $formatCode = "Y-m";
                $formatView = "F";
                break;
        }

        // Hacemos el sumatorio de número de datos por tipo de formato de fecha
        $output=array();
        $categoriesDateFormat = array();
        foreach($data as $key => $value)
        {
            /** @var \DateTime $datetime */
            $datetime = new \DateTime($key);
            if ($datetime != null) {
                //$day = $datetime->format("Y-m-d");
                //ld($formatCode);
                //ld($datetime);
                //ld($datetime->format('Y-m'));
                $dateFormatCode = $datetime->format($formatCode);
                $dateFormatView = $datetime->format($formatView);
                if (!isset($output[$dateFormatCode])) {
                    $output[$dateFormatCode] = 0;
                    $categoriesDateFormat[] = $dateFormatView;
                }
                $output[$dateFormatCode] += $value['total'];
            }
        }

        // Preparamos los datos
        foreach($output as $key => $value) {
            $values[] = $value;
        }

        // Preparamos las etiquetas (TODO)

        $return['data'] = $values;
        $return['label'] = $categoriesDateFormat;


        return $return;
    }

    private function filterByModeDate($data, $modeDate = "month") {
        switch ($modeDate) {
            case "day":
                $formatCode = "Y-m-d";
                $formatView = "d-m-Y";
                break;

            case "week":
                $formatCode = "Y-W";
                $formatView = "l, d-m-Y";
                break;

            case "month":
                $formatCode = "Y-m";
                $formatView = "F";
                break;
        }

        // Hacemos el sumatorio de número de datos por tipo de formato de fecha
        $output=array();
        $categoriesDateFormat = array();
        foreach($data as $object)
        {
            /** @var \DateTime $datetime */
            $datetime = $object->getCreatedAt();
            if ($datetime != null) {
                //$day = $datetime->format("Y-m-d");
                $dateFormatCode = $datetime->format($formatCode);
                $dateFormatView = $datetime->format($formatView);
                if (!isset($output[$dateFormatCode])) {
                    $output[$dateFormatCode] = 0;
                    $categoriesDateFormat[] = $dateFormatView;
                }
                $output[$dateFormatCode] += 1;
            }
        }

        // Preparamos los datos
        foreach($output as $key => $value) {
            $values[] = $value;
        }

        // Preparamos las etiquetas (TODO)

        $return['data'] = $values;
        $return['label'] = $categoriesDateFormat;


        return $return;
    }
}