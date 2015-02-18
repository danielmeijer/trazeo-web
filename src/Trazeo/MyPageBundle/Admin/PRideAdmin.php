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
        $collection->remove('edit');
        $collection->remove('delete');
        $collection->remove('create');
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
            //->add('groupid')
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
            ->add('id')
            ->add('groupObject')
            ->add('createdAt')
            ->add('countChildsR')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array()
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
            ->add('groupObject')
            ->add('stringChildsR')
            ->add('createdAt')
            ->add('duration')
            ->add('distance')
        ;
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

            $query->andWhere($query->getRootAlias() . '.groupid IN (:group_ids)');
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
                    'Inicio' => $dateYesterday->format('Y-m-d'),
                    'Fin' => $dateYesterday->format('Y-m-d')
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
                    'Inicio' => $dateToday->format('Y-m-d'),
                    'Fin' => $dateToday->format('Y-m-d')
                )
            );

            // Add filters to uri of tab
            $menu->addChild('HOY', array('uri' => $this->generateUrl('list', array(
                'filter' => $filterParameters
            ))));



           // ldd($filterParameters);

            /**
            $filterParameters['state'] = array(
                'createdAt'  => EqualType::TYPE_IS_EQUAL, // equal to
            );
             * */

            // Add filters to uri of tab
            /**
            $filterParameters['state']['value'] = "02/17/2015";
            $menu->addChild('AYER', array('uri' => $this->generateUrl('list', array(
                'createdAt' => $filterParameters
            ))));
             * */

            //?filter[createdAt][type]=&filter[createdAt][value][Inicio]=2015-02-01&filter[createdAt][value][Fin]=2015-02-02&filter[_page]=1&filter[_sort_by]=createdAt&filter[_sort_order]=DESC&filter[_per_page]=32

            //?createdAt[_sort_order]=DESC&createdAt[_sort_by]=createdAt&createdAt[_page]=1&createdAt[_per_page]=32&createdAt[createdAt][value][Inicio]=02%2F17%2F2015&createdAt[createdAt][value][Fin]=03%2F17%2F2015

            //?createdAt[_sort_order]=DESC&createdAt[_sort_by]=createdAt&createdAt[_page]=1&createdAt[_per_page]=32&createdAt[state][createdAt]=1&createdAt[state][value]=02%2F17%2F2015&createdAt[createdAt][value][Inicio]=02%2F17%2F2015&createdAt[createdAt][value][Fin]=03%2F17%2F2015

/**
            $filterParameters['createdAt']['value'] = array();
            //$filterParameters['createdAt']['value']['Inicio'] = "02/17/2015";
            //$filterParameters['createdAt']['value']['Fin'] = "03/17/2015";
            $menu->addChild('HOY', array('uri' => $this->generateUrl('list', array(
                'filter[createdAt][type]' => $filterParameters
            ))));
 * */
            return;
        }
    }
}