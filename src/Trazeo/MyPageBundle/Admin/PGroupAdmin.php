<?php

namespace Trazeo\MyPageBundle\Admin;

use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Security\Core\SecurityContextInterface;

use Knp\Menu\ItemInterface as MenuItemInterface;
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\BaseBundle\Entity\EGroup;

class PGroupAdmin extends Admin
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

    public function setContainer(\Symfony\Component\DependencyInjection\ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            //->add('id')
            //->add('visibility')
            //->add('hasRide')
            //->add('name')
            //->add('admin')
            //->add('childs')
            //->add('route')
            //->add('inviteGroup')
            //->add('ride')
            //->add('createdAt')
            //->add('updatedAt')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            //->addIdentifier('id')
            //->add('visibility')
            //->add('hasRide')
            ->addIdentifier('name')
            //->add('admin')
            //->add('childs')
            ->add('numberChilds')
            ->add('numberUsers')
            //->add('userextendgroups')
            //->add('monitor_userextendgroups')
            //->add('userextendgroups.user.message')
            //->add('route')
            //->add('inviteGroup')
            //->add('ride')
            //->add('page')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
            //->add('createdAt')
            //->add('updatedAt')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('admin')
            ->add('childs')
            ->add('userextendgroups')
            ->add('privateMonitor')
            ->add('monitor_userextendgroups')
            //->add('route')
            ->add('bymode', 'choice', array('choices' => array(
                EGroup::BYMODE_PEDIBUS => "Pedibús",
                EGroup::BYMODE_BICIBUS => "Bicibús",
            ) ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            //->add('visibility')
            //->add('hasRide')
            ->add('name')
            ->add('admin')
            ->add('childs')
            ->add('userextendgroups')
            ->add('privateMonitor')
            ->add('monitor_userextendgroups')
            //->add('route')
            ->add('bymode', 'choice', array('choices' => array(
                EGroup::BYMODE_PEDIBUS => "Pedibús",
                EGroup::BYMODE_BICIBUS => "Bicibús",
            ) ))
        ;
    }

    public function createQuery($context = 'list')
    {
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

            $query->where($query->getRootAlias() . '.id=0');
            foreach($page->getGroups() as $group) {
                $query->orWhere($query->getRootAlias() . '.id=' . $group->getId());
            }
        }

        return $query;
    }

    public function preUpdate($group)
    {
        // Si elimino un padre se quitan los niños que pueda tener

        /** @var EGroup $group */
        /** @var EChild $child */
        foreach($group->getChilds() as $child) {
            $is_child = false;
            foreach($child->getUserextendchilds() as $userC) {
                foreach($group->getUserextendgroups() as $userG) {
                    if ($userC->getId() == $userG->getId()) $is_child = true;
                }
            }
            if (!$is_child) {
                $group->removeChild($child);
            }
        }
    }
}