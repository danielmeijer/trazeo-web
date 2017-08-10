<?php

namespace Trazeo\BaseBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ECatalogItemAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('points', null, array('label' => 'list.label_points'))
            ->add('company', null, array('label' => 'list.label_company'))
            ->add('title', null, array('label' => 'list.label_title'))
            ->add('description', null, array('label' => 'list.label_description'))
            ->add('link', null, array('label' => 'list.label_link'))
            ->add('file', null, array('label' => 'list.label_file'))
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
            ->add('points', null, array('label' => 'list.label_points'))
            ->add('company', null, array('label' => 'list.label_company'))
            ->add('title', null, array('label' => 'list.label_title'))
            ->add('description', null, array('label' => 'list.label_description'))
            ->add('link', null, array('label' => 'list.label_link'))
            ->add('file', null, array('label' => 'list.label_file'))
            ->add('createdAt', null, array('label' => 'show.label_created_at'))
            ->add('updatedAt', null, array('label' => 'show.label_updated_at'))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('points', null, array('label' => 'list.label_points'))
            ->add('company', null, array('label' => 'list.label_company'))
            ->add('title', null, array('label' => 'list.label_title'))
            ->add('description', null, array('label' => 'list.label_description'))
            ->add('link', null, array('label' => 'list.label_link'))
            ->add('file', null, array('label' => 'list.label_file'))
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
            ->add('points', null, array('label' => 'list.label_points'))
            ->add('company', null, array('label' => 'list.label_company'))
            ->add('title', null, array('label' => 'list.label_title'))
            ->add('description', null, array('label' => 'list.label_description'))
            ->add('link', null, array('label' => 'list.label_link'))
            ->add('file', null, array('label' => 'list.label_file'))
            ->add('createdAt', null, array('label' => 'show.label_created_at'))
            ->add('updatedAt', null, array('label' => 'show.label_updated_at'))
        ;
    }
    public function getFormTheme()
    {
        return array_merge(
            parent::getFormTheme(),
            array('TrazeoBaseBundle:CRUD:base_file_field.html.twig')
        );
    }
  /*  public function getFormTheme() {
        return array('TrazeoBaseBundle:CRUD:base_file_field.html.twig');
    }*/

}
