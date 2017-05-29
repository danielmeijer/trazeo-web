<?php

namespace Trazeo\BaseBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class EChildAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
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
            ->add('medals')
            ->add('createdAt')
            ->add('updatedAt');
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('nick')
            ->add('userextendchilds')
            ->add('dateBirth')
            ->add('visibility')
            ->add('gender')
            ->add('selected')
            ->add('ride')
            ->add('groups')
            ->add('inviteChild')
            ->add('scholl')
            ->add('createdAt')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('nick')
            ->add('dateBirth')
            //->add('visibility')
            ->add('gender')
            ->add('selected')
            ->add('ride')
            ->add('inviteChild')
            ->add('scholl')
            ->add('medals', 'sonata_type_model', array('multiple' => true, 'by_reference' => false))
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
            ->add('scholl')
            ->add('medals')
        ;
    }
}
