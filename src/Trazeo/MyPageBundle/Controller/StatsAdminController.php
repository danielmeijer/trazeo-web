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
use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\HttpFoundation\Request;
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\BaseBundle\Entity\EChildRepository;
use Trazeo\BaseBundle\Entity\EGroup;
use Trazeo\BaseBundle\Entity\EGroupRepository;
use Trazeo\BaseBundle\Entity\ERide;
use Trazeo\BaseBundle\Entity\ERideRepository;
use Trazeo\BaseBundle\Entity\UserExtend;
use Trazeo\BaseBundle\Entity\UserExtendRepository;
use Trazeo\BaseBundle\Service\Helper;
use Trazeo\MyPageBundle\Form\BarAdminType;
use Trazeo\MyPageBundle\Form\EvolutionAdminType;
use Trazeo\MyPageBundle\Form\GlobalAdminType;
use Trazeo\MyPageBundle\Form\RegisteredAdminType;

/**
 * @Route("/admin/stats")
 */
class StatsAdminController extends Controller
{
    /**
     * @Route("/global/", name="stats_global")
     * @Template()
     */
    public function globalAction(Request $request) {
        // http://blog.eike.se/2014/03/custom-page-controller-in-sonata-admin.html
        $admin_pool = $this->get('sonata.admin.pool');

        $form_filters = $this->createForm(new GlobalAdminType($this));

        $data = array();

        /** @var Helper $helper */
        $helper = $this->get('trazeo_base_helper');

        if ($request->getMethod() == "POST") {
            $data = $request->get('GlobalAdmin');
            $form_filters->handleRequest($request);

            // Datos por PASEOS
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
                $date_temp_formated = $helper->getDateTime($data['date_from']);
            $qb->andWhere('r.createdAt >= :date_from');
            $qb->setParameter('date_from', $date_temp_formated->format('Y-m-d'));
            }

            if (isset($data['date_to']) && $data['date_to'] != "") {
                $date_temp_formated = $helper->getDateTime($data['date_to']);
            $qb->andWhere('r.createdAt <= :date_to');
            $qb->setParameter('date_to', $date_temp_formated->format('Y-m-d'));
            }

            $rides = $qb->getQuery()->getResult();

            $data = $helper->getDataFromRides($rides);
        }

        return array(
            'admin_pool' => $admin_pool,
            'form_filters' => $form_filters->createView(),
            'data' => $data
        );
    }

    /**
     * @Route("/bar/", name="stats_bar")
     * @Template()
     */
    public function barAction(Request $request) {
        // http://blog.eike.se/2014/03/custom-page-controller-in-sonata-admin.html
        $admin_pool = $this->get('sonata.admin.pool');
        /** @var Translator $translator */
        $translator = $this->get('translator');

        $form_filters = $this->createForm(new BarAdminType($this));

        $obEdad = null;
        $obSexo = null;
        $obColegio = null;
        $obGrupo = null;
        $obUsersColegio = null;
        $obUsersGrupo = null;

        $helper = $this->get('trazeo_base_helper');

        if ($request->getMethod() == "POST") {
            $data = $request->get('BarAdmin');
            $form_filters->handleRequest($request);

            // Datos por USUARIOS REGISTRADOS
            /** @var UserExtendRepository $repositoryUserExtend */
            $repositoryUserExtend = $this->getDoctrine()->getRepository('TrazeoBaseBundle:UserExtend');

            /** @var QueryBuilder $qb */
            $qb = $repositoryUserExtend->createQueryBuilder("ue");

            // Filtro por Grupos
            $group_ids = $this->getGroupsIDs($data);
            $qb->leftJoin('ue.groups','g');
            $qb->where('g.id IN (:group_ids)');
            $qb->setParameter('group_ids', $group_ids);
            $qb->leftJoin('ue.user', 'fuser');

            // Filtro por Fecha
            if (isset($data['date_from']) && $data['date_from'] != "") {
                $date_temp_formated = $helper->getDateTime($data['date_from']);
                $qb->andWhere('fuser.createdAt >= :date_from');
                $qb->setParameter('date_from', $date_temp_formated->format('Y-m-d'));
            }

            if (isset($data['date_to']) && $data['date_to'] != "") {
                $date_temp_formated = $helper->getDateTime($data['data_to']);
                $qb->andWhere('fuser.createdAt <= :date_to');
                $qb->setParameter('date_to', $date_temp_formated->format('Y-m-d'));
            }

            $users = $qb->getQuery()->getResult();

            // Calculamos Datos
            $schools = array();
            $groups = array();
            /** @var UserExtend $user */
            foreach($users as $user) {
                // Por Colegio
                $childs = $user->getChilds();
                if (count($childs) > 0) {
                    $child = $childs[0];
                    $id = strtolower($child->getScholl());
                    if ($id != "") {
                        if (!isset($schools[$id])) {
                            $schools[$id] = 0;
                        }
                        $schools[$id]++;;
                    }
                }

                // Por Grupos
                if (count($user->getChilds()) > 0) {
                    foreach ($user->getGroups() as $group) {
                        if (in_array($group->getId(), $group_ids)) {
                            if (!isset($groups[$group->getId()])) $groups[$group->getId()] = 0;
                            $groups[$group->getId()]++;
                        }
                    }
                }
            }

            $data_users_schools = array();
            foreach($schools as $key => $value) {
                $data_temp = array((string) $key, $value);
                $data_users_schools[] = $data_temp;
            }

            $data_users_groups = array();
            $repositoryEGroup = $this->getDoctrine()->getRepository('TrazeoBaseBundle:EGroup');
            foreach($groups as $key => $value) {
                /** @var EGroup $group */
                $group = $repositoryEGroup->findOneById($key);
                $data_temp = array($group->getName(), $value);
                $data_users_groups[] = $data_temp;
            }


            // Datos por NIÑOS
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
                $date_temp_formated = $helper->getDateTime($data['date_from']);
                $qb->andWhere('c.createdAt >= :date_from');
                $qb->setParameter('date_from', $date_temp_formated->format('Y-m-d'));
            }

            if (isset($data['date_to']) && $data['date_to'] != "") {
                $date_temp_formated = $helper->getDateTime($data['date_to']);
                $qb->andWhere('c.createdAt <= :date_to');
                $qb->setParameter('date_to', $date_temp_formated->format('Y-m-d'));
            }

            $childs = $qb->getQuery()->getResult();

            // Calculamos Datos
            $years = array();
            $boys = 0;
            $schools = array();
            $groups = array();
            /** @var EChild $child */
            foreach($childs as $child) {
                // Por Género
                if ($child->getGender() == EChild::GENDER_BOY) {
                    $boys++;
                }
                // Por Edad
                if (!isset($years[$child->getYears()])) $years[$child->getYears()] = 0;
                $years[$child->getYears()]++;
                // Por Colegio
                $id = strtolower($child->getScholl());
                if ($id != "") {
                    if (!isset($schools[$id])) {
                        $schools[$id] = 0;
                    }
                    $schools[$id]++;;
                }
                // Por Grupos
                foreach($child->getGroups() as $group) {
                    if (in_array($group->getId(), $group_ids)) {
                        if (!isset($groups[$group->getId()])) $groups[$group->getId()] = 0;
                        $groups[$group->getId()]++;
                    }
                }
            }
            $girls = count($childs) - $boys;

            ksort($years);
            $data_years = array();
            foreach($years as $key => $value) {
                $data_temp = array((string) $key . " Años", $value);
                $data_years[] = $data_temp;
            }

            $data_schools = array();
            foreach($schools as $key => $value) {
                $data_temp = array((string) $key, $value);
                $data_schools[] = $data_temp;
            }

            $data_groups = array();
            $repositoryEGroup = $this->getDoctrine()->getRepository('TrazeoBaseBundle:EGroup');
            foreach($groups as $key => $value) {
                /** @var EGroup $group */
                $group = $repositoryEGroup->findOneById($key);
                $data_temp = array($group->getName(), $value);
                $data_groups[] = $data_temp;
            }

            $label_years = array();
            foreach($data_years as $dt) {
                $label_years[] = $dt[0];
            }
            // Gráfico EDAD
            $obEdad = new Highchart();
            $obEdad->chart->renderTo('edad');
            $obEdad->title->text($translator->trans('graphic.age'));
            $obEdad->xAxis->categories($label_years);
            $obEdad->xAxis->title(array('text'  => $translator->trans('graphic.age2')));
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
            $obSexo->xAxis->title(array('text'  => "Genero"));
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
            $obSexo->series(array(array('type' => 'pie', 'name' => 'Nº niños/as', 'data' => $data_gender)));

            // Gráfico COLEGIO
            $obColegio = new Highchart();
            $obColegio->chart->renderTo('colegio');
            $obColegio->title->text('Gráfico por Colegio');
            $obColegio->xAxis->title(array('text'  => "Colegio"));
            $obColegio->xAxis->categories($data_schools);
            $obColegio->plotOptions->pie(array(
                'allowPointSelect'  => true,
                'cursor'    => 'pointer',
                'dataLabels'    => array('enabled' => false),
                'showInLegend'  => true
            ));
            $obColegio->series(array(array('type' => 'pie', 'name' => 'Nº niños/as', 'data' => $data_schools)));

            // Gráfico por GRUPOS
            $obGrupo = new Highchart();
            $obGrupo->chart->renderTo('grupo');
            $obGrupo->title->text('Gráfico por Grupo');
            $obGrupo->xAxis->categories($data_groups);
            $obGrupo->xAxis->title(array('text'  => "Grupo"));
            $obGrupo->plotOptions->pie(array(
                'allowPointSelect'  => true,
                'cursor'    => 'pointer',
                'dataLabels'    => array('enabled' => false),
                'showInLegend'  => true
            ));
            $obGrupo->series(array(array('type' => 'column', 'name' => 'Nº niños/as', 'data' => $data_groups)));

            // Gráficos de USUARIOS/FAMILIAS
            // Gráfico COLEGIO
            $obUsersColegio = new Highchart();
            $obUsersColegio->chart->renderTo('users_colegio');
            $obUsersColegio->title->text('Gráfico por Colegio');
            $obUsersColegio->xAxis->title(array('text'  => "Colegio"));
            $obUsersColegio->xAxis->categories($data_users_schools);
            $obUsersColegio->plotOptions->pie(array(
                'allowPointSelect'  => true,
                'cursor'    => 'pointer',
                'dataLabels'    => array('enabled' => false),
                'showInLegend'  => true
            ));
            $obUsersColegio->series(array(array('type' => 'pie', 'name' => 'Nº familias', 'data' => $data_users_schools)));

            // Gráfico por GRUPOS
            $obUsersGrupo = new Highchart();
            $obUsersGrupo->chart->renderTo('users_grupo');
            $obUsersGrupo->title->text('Gráfico por Grupo');
            $obUsersGrupo->xAxis->categories($data_users_groups);
            $obUsersGrupo->xAxis->title(array('text'  => "Colegio"));
            $obUsersGrupo->plotOptions->pie(array(
                'allowPointSelect'  => true,
                'cursor'    => 'pointer',
                'dataLabels'    => array('enabled' => false),
                'showInLegend'  => true
            ));
            $obUsersGrupo->series(array(array('type' => 'column', 'name' => 'Nº familias', 'data' => $data_users_groups)));
        }

        return array(
            'admin_pool' => $admin_pool,
            'form_filters' => $form_filters->createView(),
            'chartEdad' => $obEdad,
            'chartSexo' => $obSexo,
            'chartColegio' => $obColegio,
            'chartGrupo' => $obGrupo,
            'chartUsersColegio' => $obUsersColegio,
            'chartUsersGrupo' => $obUsersGrupo
        );
    }

    /**
     * @Route("/registered/", name="stats_registered")
     * @Template();
     */
    public function registeredAction(Request $request) {
        // http://blog.eike.se/2014/03/custom-page-controller-in-sonata-admin.html
        $admin_pool = $this->get('sonata.admin.pool');

        $form_filters = $this->createForm(new RegisteredAdminType($this));

        $chartByUser = null;
        $chartByChild = null;
        $chartTotal = null;
        $chartChildren = null;
        $chartUsers = null;

        $helper = $this->get('trazeo_base_helper');

        if ($request->getMethod() == "POST") {
            $data = $request->get('RegisteredAdmin');
            $form_filters->handleRequest($request);

            // Datos por USUARIOS REGISTRADOS
            /** @var UserExtendRepository $repositoryUserExtend */
            $repositoryUserExtend = $this->getDoctrine()->getRepository('TrazeoBaseBundle:UserExtend');

            /** @var QueryBuilder $qb */
            $qb = $repositoryUserExtend->createQueryBuilder("ue");

            // Filtro por Grupos
            $group_ids = $this->getGroupsIDs($data);
            $qb->leftJoin('ue.groups','g');
            $qb->where('g.id IN (:group_ids)');
            $qb->setParameter('group_ids', $group_ids);
            $qb->leftJoin('ue.user', 'fuser');

            // Filtro por Fecha
            if (isset($data['date_from']) && $data['date_from'] != "") {
                $date_temp_formated = $helper->getDateTime($data['date_from']);
                $qb->andWhere('fuser.createdAt >= :date_from');
                $qb->setParameter('date_from', $date_temp_formated->format('Y-m-d'));
            }

            if (isset($data['date_to']) && $data['date_to'] != "") {
                $date_temp_formated = $helper->getDateTime($data['date_to']);
                $qb->andWhere('fuser.createdAt <= :date_to');
                $qb->setParameter('date_to', $date_temp_formated->format('Y-m-d'));
            }

            $users = $qb->getQuery()->getResult();

            $return_users = array();
            /** @var UserExtend $userExtend */
            foreach($users as $userExtend) {
                $createdAt = $userExtend->getUser()->getCreatedAt();
                $createdAtF = $createdAt->format('Y-m-d');
                foreach($userExtend->getGroups() as $group) {
                    if (in_array($group->getId(), $group_ids) && count($userExtend->getChilds()) > 0) {
                        if (!isset($return_users[$group->getId()])) {
                            $return_users[$group->getId()] = array();
                        }
                        if (!isset($return_users[$group->getId()][$createdAtF])) {
                            $return_users[$group->getId()][$createdAtF] = array();
                            $return_users[$group->getId()][$createdAtF]['total'] = 0;
                        }
                        $return_users[$group->getId()][$createdAtF]['total'] += 1;
                    }
                }
            }
            $return_users = $this->fixFillBiArray($return_users);

            //ldd($return_users);

            // Datos por NIÑOS
            /** @var EChildRepository $repositoryChildRepository */
            $repositoryChildRepository = $this->getDoctrine()->getRepository('TrazeoBaseBundle:EChild');

            /** @var QueryBuilder $qb */
            $qb = $repositoryChildRepository->createQueryBuilder("c");

            // Filtro por Grupos
            $group_ids = $this->getGroupsIDs($data);
            $qb->leftJoin('c.groups','g');
            $qb->where('g.id IN (:group_ids)');
            $qb->setParameter('group_ids', $group_ids);

            // Filtro por Fecha
            if (isset($data['date_from']) && $data['date_from'] != "") {
                $date_temp_formated = $helper->getDateTime($data['date_from']);
                $qb->andWhere('c.createdAt >= :date_from');
                $qb->setParameter('date_from', $date_temp_formated->format('Y-m-d'));
            }

            if (isset($data['date_to']) && $data['date_to'] != "") {
                $date_temp_formated = $helper->getDateTime($data['date_to']);
                $qb->andWhere('c.createdAt <= :date_to');
                $qb->setParameter('date_to', $date_temp_formated->format('Y-m-d'));
            }

            $childs = $qb->getQuery()->getResult();

            $return_childs = array();
            /** @var EChild $child */
            foreach($childs as $child) {
                $createdAt = $child->getCreatedAt();
                $createdAtF = $createdAt->format('Y-m-d');
                foreach($child->getGroups() as $group) {
                    if (in_array($group->getId(), $group_ids)) {
                        $gID = $group->getId();
                        if (!isset($return_childs[$gID])) {
                            $return_childs[$gID] = array();
                        }
                        if (!isset($return_childs[$gID][$createdAtF])) {
                            $return_childs[$gID][$createdAtF] = array();
                            $return_childs[$gID][$createdAtF]['total'] = 0;
                        }
                        $return_childs[$gID][$createdAtF]['total'] += 1;
                    }
                }
            }
            $return_childs = $this->fixFillBiArray($return_childs);

            //ld($return_childs);

            // Gráfica TOTAL
            /** @var EGroupRepository $repositoryEGroup */
            $repositoryEGroup = $this->getDoctrine()->getRepository('TrazeoBaseBundle:EGroup');

            // Gráfica con línea por cada Grupo
            $series = array();
            $labels = array();

            // Líneas de niños/as
            $i = 0;
            foreach($return_childs as $key => $value) {
                // Chart
                /** @var EGroup $group */
                $group = $repositoryEGroup->findOneById($key);
                $formattedData = $this->filterByModeDatePlus($value, $data['mode']);
                if (count($formattedData['label']) > count($labels)) {
                    $labels = $formattedData['label'];
                }
                $serie = array(
                    "name" => "NIÑOS/AS: " . $group->getName(),
                    "data" => $formattedData['data'],
                    "dashStyle" => 'longdash',
                    "color" => $this->getColorForGroupID($i));
                $series_child[] = $serie;
                $series[] = $serie;
                $i++;

                //ld($labels);
                //ld($series);
                //ldd($formattedData);
            }
            $chartChildren = new Highchart();
            $chartChildren->chart->renderTo('linechartByChildren');  // The #id of the div where to render the chart
            $chartChildren->title->text('Evolución de registros de Niños');
            $chartChildren->xAxis->title(array('text'  => "Fecha"));
            $chartChildren->xAxis->categories($labels);
            $chartChildren->yAxis->title(array('text'  => "Número de Registros"));
            $chartChildren->series($series);

            $series = array();
            $labels = array();
            $i = 0;
            // Líneas de usuarios/familias
            foreach($return_users as $key => $value) {
                // Chart
                /** @var EGroup $group */
                $group = $repositoryEGroup->findOneById($key);
                $formattedData = $this->filterByModeDatePlus($value, $data['mode']);
                if (count($formattedData['label']) > count($labels)) {
                   $labels = $formattedData['label'];
                }
                $serie = array(
                    "name" => "FAMILIAS: " . $group->getName(),
                    "data" => $formattedData['data'],
                    "color" => $this->getColorForGroupID($i)
                );
                $series[] = $serie;
                $i++;
                //ld($labels);
                //ldd($serie);
            }
            $chartUsers = new Highchart();
            $chartUsers->chart->renderTo('linechartByUser');  // The #id of the div where to render the chart
            $chartUsers->title->text('Evolución de registros de Familias');
            $chartUsers->xAxis->title(array('text'  => "Fecha"));
            $chartUsers->xAxis->categories($labels);
            $chartUsers->yAxis->title(array('text'  => "Número de Registros"));
            $chartUsers->series($series);


            // Chart TOTAL (YA NO SE USA)
            /**
            $chartTotal = new Highchart();
            $chartTotal->chart->renderTo('linechartByTotal');  // The #id of the div where to render the chart
            $chartTotal->title->text('Evolución de registros');
            $chartTotal->xAxis->title(array('text'  => "Fecha"));
            $chartTotal->xAxis->categories($labels);
            $chartTotal->yAxis->title(array('text'  => "Número de Registros"));
            $chartTotal->series($series);
            **/
            //ld($labels);
            //ldd($series);

            //ldd($data);
        }

        return array(
            'admin_pool' => $admin_pool,
            'form_filters' => $form_filters->createView(),
            'chartChildren' => $chartChildren,
            'chartUsers' => $chartUsers
        );
    }

    private function getColorForGroupID($groupID) {
        $colorkey = $groupID % 10;
        switch ($colorkey) {
            case 0: return "#770000";
            case 1: return "#F0000A";
            case 2: return "#A90AA1";
            case 3: return "#001299";
            case 4: return "#90A090";
            case 5: return "#00FF00";
            case 6: return "#000FF0";
            case 7: return "#0000FF";
            case 8: return "#F0000F";
            case 9: return "#F0F000";
        }
    }

    /**
     * @Route("/evolution/", name="stats_evolution")
     * @Template()
     */
    public function evolutionAction(Request $request)
    {
        // http://blog.eike.se/2014/03/custom-page-controller-in-sonata-admin.html
        $admin_pool = $this->get('sonata.admin.pool');

        //$child = new EChild();
        $form_filters = $this->createForm(new EvolutionAdminType($this));

        $helper = $this->get('trazeo_base_helper');

        $chartByGroup = null;
        $chartTotal = null;

        if ($request->getMethod() == "POST") {
            $data = $request->get('EvolutionAdmin');
            $form_filters->handleRequest($request);

            /** @var ERideRepository $repositoryERide */
            $repositoryERide = $this->getDoctrine()->getRepository('TrazeoBaseBundle:ERide');
            /** @var QueryBuilder $qb */
            $qb = $repositoryERide->createQueryBuilder("r");

            // He cambiado el groupsids por groupRegistered
            // Filtro por Grupos
            $group_ids = $this->getGroupsIDs($data);
            $qb->where('r.groupRegistered IN (:group_ids)');
            $qb->setParameter('group_ids', $group_ids);

            // Filtro por Fecha
            if (isset($data['date_from']) && $data['date_from'] != "") {
                $date_temp_formated = $helper->getDateTime($data['date_from']);
                $qb->andWhere('r.createdAt >= :date_from');
                $qb->setParameter('date_from', $date_temp_formated->format('Y-m-d'));
            }

            if (isset($data['date_to']) && $data['date_to'] != "") {
                $date_temp_formated = $helper->getDateTime($data['date_to']);
                $qb->andWhere('r.createdAt <= :date_to');
                $qb->setParameter('date_to', $date_temp_formated->format('Y-m-d'));
            }

            $return = array();
            $total = array();

            //TODO: DEBUG, todos los paseos - $qb = $repositoryERide->createQueryBuilder("r");
            $rides = $qb->getQuery()->getResult();

            /** @var ERide $ride */
            foreach($rides as $ride) {
                //ld($ride->getId());
                $children_count = $repositoryERide->getFixCountChildrenInRide($ride);
                if ($children_count > 0) {
                    // Por Grupos
                    if (!isset($return[$ride->getGroupid()])) $return[$ride->getGroupid()] = array();
                    if (!isset($return[$ride->getGroupid()][$ride->getCreatedAt()->format('Y-m-d')])) {
                        $return[$ride->getGroupid()][$ride->getCreatedAt()->format('Y-m-d')] = array();
                        $return[$ride->getGroupid()][$ride->getCreatedAt()->format('Y-m-d')]['total'] = 0;
                        $return[$ride->getGroupid()][$ride->getCreatedAt()->format('Y-m-d')]['ride'] = $ride;
                    }

                    $return[$ride->getGroupid()][$ride->getCreatedAt()->format('Y-m-d')]['total'] += $children_count;

                    // Total
                    if (!isset($total[$ride->getCreatedAt()->format('Y-m-d')])) {
                        $total[$ride->getCreatedAt()->format('Y-m-d')] = array();
                        $total[$ride->getCreatedAt()->format('Y-m-d')]['total'] = 0;
                    }
                    $total[$ride->getCreatedAt()->format('Y-m-d')]['total'] += $children_count;
                }
            }
            $return = $this->fixFillBiArray($return);

            //ldd($return);

            // Gráfica Global
            $formattedData = $this->filterByModeDatePlus($total, $data['mode']);
            $labels = $formattedData['label'];

            $serie = array("name" => "Total", "data" => $formattedData['data']);
            $series[] = $serie;

            $chartTotal = new Highchart();
            $chartTotal->chart->renderTo('linechartTotal');  // The #id of the div where to render the chart
            $chartTotal->title->text('Evolución de participación Total');
            $chartTotal->xAxis->title(array('text'  => "Fecha"));
            $chartTotal->xAxis->categories($labels);
            $chartTotal->yAxis->title(array('text'  => "Número de Participaciones"));
            $chartTotal->series($series);

            /** @var EGroupRepository $repositoryEGroup */
            $repositoryEGroup = $this->getDoctrine()->getRepository('TrazeoBaseBundle:EGroup');
            // Gráfica con línea por cada Grupo
            $series = array();
            foreach($return as $key => $value) {
                // Chart
                /** @var EGroup $group */
                $group = $repositoryEGroup->findOneById($key);
                $formattedData = $this->filterByModeDatePlus($value, $data['mode']);
                //ldd($formattedData);
                $serie = array("name" => $group->getName(), "data" => $formattedData['data']);
                $series[] = $serie;
            }

            $chartByGroup = new Highchart();
            $chartByGroup->chart->renderTo('linechartByGroups');  // The #id of the div where to render the chart
            $chartByGroup->title->text('Evolución de participación por Grupos');
            $chartByGroup->xAxis->title(array('text'  => "Fecha"));
            $chartByGroup->xAxis->categories($labels);
            $chartByGroup->yAxis->title(array('text'  => "Número de Participaciones"));
            $chartByGroup->series($series);
        }

        return array(
            'admin_pool' => $admin_pool,
            'form_filters' => $form_filters->createView(),
            'chartByGroup' => $chartByGroup,
            'chartTotal' => $chartTotal
        );
    }

    /**
     * Rellena los datos de un Array bidimensional con 0
     * en los huecos que no tienen datos
     *
     * @param $biArray
     */
    private function fixFillBiArray($biArray) {
        foreach($biArray as $key1 => $array_value1) {
            foreach($array_value1 as $keyv1 => $value1) {
                foreach($biArray as $key2 => $array_value2) {
                    if (!isset($biArray[$key2][$keyv1])) {
                        $biArray[$key2][$keyv1] = array();
                        $biArray[$key2][$keyv1]['total'] = 0;
                    }
                }
            }
        }

        // Ordenamos los Arrays por fecha
        $newArray = array();
        foreach($biArray as $key => $array) {
            ksort($array);
            $newArray[$key] = $array;
        }

        return $newArray;
    }

    private function getGroupsIDs($data) {
        // Filtro por Grupos
        if (isset($data['group'])) {
            $group_ids = $data['group'];
        } else {
            /** @var Helper $helper */
            $helper = $this->container->get('trazeo_base_helper');
            $page = $helper->getPageBySubdomain();
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
        // Order data by Date
        ksort($data);

        setlocale(LC_ALL,"es_ES");

        $helper = $this->container->get('trazeo_base_helper');

        switch ($modeDate) {
            case "day":
                $formatCode = "Ymd";
                $formatView = "d-m-Y";
                break;

            case "week":
                $formatCode = "YW";
                $formatView = "l, d-m-Y";
                break;

            case "month":
                $formatCode = "Ym";
                $formatView = "F";
                break;
        }

        // Hacemos el sumatorio de número de datos por tipo de formato de fecha
        $output=array();
        $categoriesDateFormat = array();
        foreach($data as $key => $value)
        {
            /** @var \DateTime $datetime */
            $datetime = $helper->getDateTime($key);
            if ($datetime != null) {
                //$day = $datetime->format("Y-m-d");
                //ld($formatCode);
                //ld($datetime);
                //ld($datetime->format('Y-m'));
                $dateFormatCode = $datetime->format($formatCode);
                $dateFormatView = $datetime->format($formatView);
                if (!isset($output[$dateFormatCode])) {
                    $output[$dateFormatCode] = 0;
                    // $dateFormatCode KEY
                    $categoriesDateFormat[] = $dateFormatView;
                }
                $output[$dateFormatCode] += $value['total'];
            }
        }

        $values = array();
        foreach($output as $key => $value) {
            $values[] = $value;
        }

        /**
        $values = array();
        // Preparamos los datos
        foreach($output as $key => $value) {
            $d = array();
            $d[] = $key;
            $d[] = $value;
            $values[] = $d;
        }
         **/

        // Preparamos las etiquetas (TODO)

        $return['data'] = $values;
        $return['label'] = $categoriesDateFormat;
        //ldd($return);
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