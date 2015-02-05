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

        /**
        if (
            $this->hasRoute('edit') && $this->isGranted('EDIT') &&
            $this->hasRoute('delete') && $this->isGranted('DELETE')
        ) {**/
            $actions['merge'] = array(
                'label' => $this->trans('action_graph', array(), 'SonataAdminBundle'),
                'ask_confirmation' => false
            );

        //}

        return $actions;
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
            ->add('dateBirth')
            ->add('createdAt');

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
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('nick')
            ->add('dateBirth', 'sonata_type_datetime_picker')
            ->add('visibility')
            ->add('gender')
            ->add('selected')
            ->add('ride')
            ->add('inviteChild')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('nick')
            ->add('dateBirth')
            ->add('visibility')
            ->add('gender')
            ->add('selected')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function configureSideMenu(MenuItemInterface $menu, $action, AdminInterface $childAdmin = null)
    {

        if (!$childAdmin && !in_array($action, array('edit'))) {
            return;
        }

        $admin = $this->isChild() ? $this->getParent() : $this;
        $id = $admin->getRequest()->get('id');
        $menu->addChild(
            $this->trans('sidemenu.link_edit_page'),
            array('uri' => $admin->generateUrl('edit', array('id' => $id)))
        );
    }

    public function createQuery($context = 'list')
    {
        // TODO: Aquí se pueden modificar cosas

        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);
        //if(!$this->securityContext->isGranted('ROLE_SUPER_ADMIN')) {
            $user = $this->securityContext->getToken()->getUser();

            //$query->andWhere($query->getRootAlias() . '.id=' . 19);
        //}

        return $query;
    }
}
