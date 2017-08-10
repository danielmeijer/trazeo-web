<?php

namespace Trazeo\BaseBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class EUserActionAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('action', null, array('label' => 'list.label_action'))
            ->add('sopinetuserextend')
            ->add('ids')
            ->add('acumulated', null, array('label' => 'list.label_acumulated'))
            ->add('mobile')
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
            ->add('id')
            ->add('action', null, array('label' => 'list.label_action'))
            ->add('sopinetuserextend')
            ->add('ids')
            ->add('acumulated', null, array('label' => 'list.label_acumulated'))
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
            ->add('action', null, array('label' => 'list.label_action'))
            ->add('sopinetuserextend')
            ->add('ids')
            ->add('acumulated', null, array('label' => 'list.label_acumulated'))
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
            ->add('action', null, array('label' => 'list.label_action'))
            ->add('sopinetuserextend')
            ->add('ids')
            ->add('acumulated', null, array('label' => 'list.label_acumulated'))
            ->add('createdAt', null, array('label' => 'show.label_created_at'))
            ->add('updatedAt', null, array('label' => 'show.label_updated_at'))
        ;
    }
}