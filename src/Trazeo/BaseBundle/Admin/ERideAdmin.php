<?php

namespace Trazeo\BaseBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ERideAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('duration', null, array('label' => 'show.label_duration'))
            ->add('distance', null, array('label' => 'show.label_distance'))
            ->add('groupid', null, array('label' => 'list.label_group_id'))
            ->add('group', null, array('label' => 'show.label_group_object'))
            ->add('reports', null, array('label' => 'show.label_reports'))
            ->add('userextend', null, array('label' => 'list.label_userextendchilds'))
            ->add('createdAt', null, array('label' => 'show.label_created_at'))
            ->add('updatedAt', null, array('label' => 'show.label_updated_at'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('duration', null, array('label' => 'show.label_duration'))
            ->add('distance', null, array('label' => 'show.label_distance'))
            ->add('groupid', null, array('label' => 'list.label_group_id'))
            ->add('group', null, array('label' => 'show.label_group_object'))
            //->add('events')
            ->add('reports', null, array('label' => 'show.label_reports'))
            ->add('userextend', null, array('label' => 'list.label_userextendchilds'))
            ->add('createdAt', null, array('label' => 'show.label_created_at'))
            ->add('updatedAt', null, array('label' => 'show.label_updated_at'))
            ->add('_action', 'actions', array(
                'label' => 'list.label_action',
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('duration', null, array('label' => 'show.label_duration'))
            ->add('distance', null, array('label' => 'show.label_distance'))
            ->add('groupid', null, array('label' => 'list.label_group_id'))
            ->add('userextend', null, array('label' => 'list.label_userextendchilds'))
            ->add('createdAt', null, array('label' => 'show.label_created_at'))
            ->add('updatedAt', null, array('label' => 'show.label_updated_at'))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('duration', null, array('label' => 'show.label_duration'))
            ->add('distance', null, array('label' => 'show.label_distance'))
            ->add('groupid', null, array('label' => 'list.label_group_id'))
            ->add('userextend', null, array('label' => 'list.label_userextendchilds'))
            ->add('createdAt', null, array('label' => 'show.label_created_at'))
            ->add('updatedAt', null, array('label' => 'show.label_updated_at'))
        ;
    }
}
