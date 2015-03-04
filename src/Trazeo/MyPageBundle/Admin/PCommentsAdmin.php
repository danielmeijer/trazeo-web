<?php

namespace Trazeo\MyPageBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Proxies\__CG__\Trazeo\BaseBundle\Entity\UserExtend;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Tests\Fixtures\Admin\FieldDescription;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Knp\Menu\ItemInterface as MenuItemInterface;

class PCommentsAdmin extends Admin
{
    protected $datagridValues = array(
        '_page' => 1,            // display the first page (default = 1)
        '_sort_order' => 'DESC', // reverse order (default = 'ASC')
        '_sort_by' => 'createdAt'  // name of the ordered field
        // (default = the model's id field, if any)

        // the '_sort_by' key can be of the form 'mySubModel.mySubSubModel.myField'.
    );

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('create');
    }

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


    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $em = $this->container->get('doctrine');
        $em->getEntityManager()->getConfiguration()->addCustomDatetimeFunction('DATE', '\Trazeo\MyPageBundle\DQL\DateFunction');

        $datagridMapper
            ->add('author')
            ->add('createdAt', 'doctrine_orm_callback',
                array(
                    'label' => 'Fecha',
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
            //->add('thread')
            //->add('author')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('body')
            ->add('thread.id', 'string', array('template' => "TrazeoMyPageBundle:Admin:list_thread.html.twig"))
            ->add('author')
            ->add('score')
            ->add('createdAt')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array()
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
            ->add('body')
            ->add('author')
            ->add('score')
            ->add('createdAt')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('body')
            ->add('author')
            ->add('score')
        ;
    }

    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);

        // TODO: REVISAR FILTRO

        if(false && !$this->securityContext->isGranted('ROLE_SUPER_ADMIN'))
        {
            $user = $this->securityContext->getToken()->getUser();

            /** @var Helper $helper */
            $helper = $this->container->get('trazeo_base_helper');
            /** @var Page $page */
            $page = $helper->getPageBySubdomain();

            if ($page == null) die("No Project for you");

            if ($page->getUserextend()->getUser()->getId() != $user->getId()) die("No Project for you");

            $group_ids = array();
            foreach($page->getGroups() as $group) {
                $group_ids[] = $group->getId();
            }

            $query->leftJoin($query->getRootAlias() . '.groups','g');
            $query->andWhere('g.id IN (:group_ids)');
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

            return;
        }
    }
}