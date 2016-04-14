<?php

namespace Trazeo\MyPageBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Form\Type\EqualType;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\MyPageBundle\DQL\DateFunction;

use Knp\Menu\ItemInterface as MenuItemInterface;

class PRideAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'createdAt'  // name of the ordered field
        // (default = the model's id field, if any)

        // the '_sort_by' key can be of the form 'mySubModel.mySubSubModel.myField'.
    );

    /**
     * Security Context
     * @var \Symfony\Component\Security\Core\SecurityContextInterface
     */
    protected $securityContext;

    /** @var \Symfony\Component\DependencyInjection\ContainerInterface */
    private $container;

    public function setSecurityContext(SecurityContextInterface $securityContext)
    {
        $this->securityContext = $securityContext;
    }

    public function setContainer (\Symfony\Component\DependencyInjection\ContainerInterface $container) {
        $this->container = $container;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('link', $this->getRouterIdParameter().'/link');
        //$collection->remove('edit');
        //$collection->remove('delete');
        //$collection->remove('create');
    }


    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $em = $this->container->get('doctrine');
        $em->getEntityManager()->getConfiguration()->addCustomDatetimeFunction('DATE', '\Trazeo\MyPageBundle\DQL\DateFunction');

        $datagridMapper
            //->add('group')
            ->add('groupRegistered')
            ->add('createdAt', 'doctrine_orm_callback',
                array(
                    'label' => 'Fecha de los Paseos',
                    'callback' => function($queryBuilder, $alias, $field, $value) {
                        if (!isset($value['value']['Inicio'])) {
                            return;
                        }
                        /** @var \DateTime $dateINI */
                        $dateINI = $value['value']['Inicio'];
                        $inputValueINI = $dateINI->format('Y-m-d');
                        $dateFIN = $value['value']['Fin'];
                        $inputValueFIN = $dateFIN->format('Y-m-d');

                        $queryBuilder->andWhere("DATE($alias.createdAt) >= :CreatedAtINI");
                        $queryBuilder->andWhere("DATE($alias.createdAt) <= :CreatedAtFIN");

                        $queryBuilder->setParameter('CreatedAtINI', $inputValueINI);
                        $queryBuilder->setParameter('CreatedAtFIN', $inputValueFIN);
                        return true;
                    },
                    'field_type' => 'text'
                ), 'DateRangePicker')
            //->add('createdAt', 'doctrine_orm_datetime_range', array('format' => 'MM/dd/yyyy'), 'DateRangePicker')
            ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('groupRegistered')
            ->add('stringChildsR')
            ->add('createdAt')
            ->add('countChildsR')
            ->add('countReport')
            /**
            ->add('countReport',null,array(
                'sortable'=>true,
                'sort_field_mapping'=> array('fieldName'=>'distance'),
                'sort_parent_association_mappings' => array(array('fieldName'=>'distance'))
                ))
             **/
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                    'link' => array(
                        'template' => 'CRUD\list__action_link.html.twig'
                    )
                )
            ));
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('groupRegistered')
            ->add('stringChildsR')
            ->add('fixChildCount')
            ->add('createdAt')
            ->add('duration')
            ->add('distance')
            ->add('countReport')
            ->add('reports')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('distance')
            ->add('groupRegistered')
            ->add('createdAt', 'sonata_type_datetime_picker')
            ->add('fixChildCount')
        ;
    }

    public function getExportFields() {
        return array(
            'Numero de niños'=>'countChildsR',
            'Duración'=>'duration',
            'Distancía en metros'=>'distance',
            'Grupo'=>'groupRegistered.name',
            'Numero de reportes'=>'reports',
            'Fecha del paseo'=>'formatDate',
            '% del Ejercico diario'=>'diaryExercise',
            'Pasos dados'=>'steps',
            'Redución de emisiones CO2(gr)'=>'cO2Reduction',
            'Calorias consumidas(por niño)'=>'caloriesConsumption'
        );
    }

    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        if(!$this->securityContext->isGranted('ROLE_SUPER_ADMIN'))
        {
            $user = $this->securityContext->getToken()->getUser();

            /** @var Helper $helper */
            $helper = $this->container->get('trazeo_base_helper');
            /** @var Page $page */
            $page = $helper->getPageBySubdomain();

            if ($page == null) die("No Project for you");

            if ($page->getUserextend()->getUser()->getId() != $user->getId()) die("No Project for you");

            // TODO: FIX
            $group_ids = array();
            foreach($page->getGroups() as $group) {
                $group_ids[] = $group->getId();
            }

            $query->andWhere($query->getRootAlias() . '.groupRegistered IN (:group_ids)');
            $query->setParameter('group_ids', $group_ids);

        }

        return $query;
    }

    protected function configureTabMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {
        if (!$childAdmin && !in_array($action, array('edit', 'show', 'list'))) {
            return;
        }

        if ($action == 'list') {
            $filterParameters = $this->getFilterParameters();

            // AYER
            $dateYesterday = new \DateTime();
            $dateYesterday->add(\DateInterval::createFromDateString('yesterday'));

            $filterParameters['createdAt'] = array(
                'value' => array(
                    'Inicio' => $dateYesterday->format('d/m/Y'),
                    'Fin' => $dateYesterday->format('d/m/Y')
                )
            );

            // Add filters to uri of tab
            $menu->addChild('AYER', array('uri' => $this->generateUrl('list', array(
                'filter' => $filterParameters
            ))));

            // HOY
            $dateToday = new \DateTime();

            $filterParameters['createdAt'] = array(
                'value' => array(
                    'Inicio' => $dateToday->format('d/m/Y'),
                    'Fin' => $dateToday->format('d/m/Y')
                )
            );

            // Add filters to uri of tab
            $menu->addChild('HOY', array('uri' => $this->generateUrl('list', array(
                'filter' => $filterParameters
            ))));

            return;
        }
    }


}