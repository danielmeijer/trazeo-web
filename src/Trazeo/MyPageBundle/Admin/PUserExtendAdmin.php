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

use Knp\Menu\ItemInterface as MenuItemInterface;

class PUserExtendAdmin extends Admin
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

        $actions['send_email'] = array(
            'label' => $this->trans('Enviar un Email', array(), 'SonataAdminBundle'),
            'ask_confirmation' => false
        );

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
            //->add('groups', null, array(), null, array('expanded' => true, 'multiple' => true))
            ->add('groups', null, array(), null, array('multiple' => true))
            ->add('user.email')
            ->add('name')
 //           ->add('city')
   //          ->add('country')
            //->add('createdAt', 'doctrine_orm_datetime_range', array('format' => 'MM/dd/yyyy'), 'DateRangePicker')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            //->addIdentifier('id')
            ->add('user.username')
            ->add('user.email')
            ->add('name')
            ->add('mobile')
            ->add('groups')
            ->add('childs')
//            ->add('city')
//            ->add('country')
            ->add('user.createdAt')
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
            ->add('user.username')
            ->add('user.email')
            ->add('name')
            ->add('mobile')
            ->add('groups')
            ->add('childs')
            ->add('city.name')
            ->add('country.name')
            ->add('user.createdAt')
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

            $query->leftJoin($query->getRootAlias() . '.groups','g');
            $query->andWhere('g.id IN (:group_ids)');
            $query->setParameter('group_ids', $group_ids);

        }

        return $query;
    }


    /**
     * {@inheritdoc}
     */
    public function getExportFields()
    {
        return array(
            'Email'=>'user.username',
            'Fecha de registro'=>'user.createdAt',
            'Móvil'=>'mobile',
            'Ciudad'=>'city.name',
            'País'=>'country.name'
        );
    }
}