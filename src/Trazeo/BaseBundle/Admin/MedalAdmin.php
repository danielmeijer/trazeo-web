<?php

namespace Trazeo\BaseBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class MedalAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title', null, array('label' => 'list.label_title'))
            ->add('childs', null, array('label' => 'list.label_childs'))
            ->add('createdAt', null, array('label' => 'show.label_created_at'))
            ->add('updatedAt', null, array('label' => 'show.label_updated_at'));
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('title', null, array('label' => 'list.label_title'))
            ->add('description', null, array('label' => 'list.label_desc_medal'))
            ->add('image', null, array('label' => 'list.label_image'))
            ->add('childs', null, array('label' => 'list.label_childs'))
            ->add('_action', 'actions', array(
                'label' => 'list.label_action',
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
            ->add('title', null, array('label' => 'list.label_title'))
            ->add('description', null, array('label' => 'list.label_desc_medal'))
            ->add('image', null, array('label' => 'list.label_image'))
            ->add('childs', null, array('label' => 'list.label_childs'))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('title', null, array('label' => 'list.label_title'))
            ->add('description', null, array('label' => 'list.label_desc_medal'))
            ->add('image', null, array('label' => 'list.label_image'))
            ->add('childs', null, array('label' => 'list.label_childs'))
            ->add('createdAt', null, array('label' => 'show.label_created_at'))
            ->add('updatedAt', null, array('label' => 'show.label_updated_at'))
        ;
    }
}
