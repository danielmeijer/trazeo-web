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
use Symfony\Component\Security\Core\SecurityContextInterface;
use Trazeo\BaseBundle\Entity\EChild;

use Knp\Menu\ItemInterface as MenuItemInterface;

class PChildAdmin extends Admin
{
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

    public function getBatchActions()
    {
        // retrieve the default batch actions (currently only delete)
        $actions = parent::getBatchActions();

        // TODO: LA QUITAMOS
        /*
        $actions['merge'] = array(
            'label' => $this->trans('action_graph', array(), 'SonataAdminBundle'),
            'ask_confirmation' => false
        );
        */

        return $actions;
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
        $datagridMapper
            ->add('groups')
            ->add('createdAt', 'doctrine_orm_datetime_range', array('format' => 'MM/dd/yyyy'), 'DateRangePicker')
            ->add('scholl')
            // EDAD
            //->add('createdAt', 'doctrine_orm_datetime_range', array(), 'sonata_type_datetime_picker')
            ->add('gender', 'doctrine_orm_choice', [],
                'choice',
                [
                    'choices' => array(
                        EChild::GENDER_BOY => "Niño",
                        Echild::GENDER_GIRL => "Niña"
                    )
                ]
            );

        /**
            $datagridMapper
                ->add('id')
                ->add('nick')
                ->add('userextendchilds')
                ->add('dateBirth')
                ->add('visibility')
                ->add('gender')
                ->add('selected')
                ->add('ride')
                ->add('groups')
                ->add('inviteChild')
                ->add('createdAt')
                ->add('updatedAt');
        }
         * **/
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('nick')
            ->add('scholl')
            ->add('groups')
            ->add('userextendchilds')
            ->add('dateBirth')
            ->add('createdAt')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array()
                )
            ));

        /**
            $listMapper
                ->addIdentifier('id')
                ->add('nick')
                ->add('userextendchilds')
                ->add('dateBirth')
                ->add('visibility')
                ->add('gender')
                ->add('selected')
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'show' => array(),
                        'edit' => array(),
                        'delete' => array(),
                    )
                ))
                ->add('ride')
                ->add('groups')
                ->add('inviteChild')
                ->add('createdAt')
                ->add('updatedAt');
        }
         **/
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('nick')
            ->add('userextendchilds')
            ->add('dateBirth')
            ->add('visibility')
            ->add('gender')
            ->add('groups')
            ->add('ride')
            ->add('createdAt')
            ->add('updatedAt')
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
}
