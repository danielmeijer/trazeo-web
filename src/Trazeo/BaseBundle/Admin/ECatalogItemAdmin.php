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
            ->add('points')
            ->add('company')
            ->add('title')
            ->add('description')
            ->add('link') 
            ->add('file')    
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('points')
            ->add('company')
            ->add('title')
            ->add('description')
            ->add('link')     
            ->add('file')
            ->add('createdAt')
            ->add('updatedAt')  
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('points')
            ->add('company')
            ->add('title')
            ->add('description')
            ->add('link')       
            ->add('file')
            ->add('createdAt')
            ->add('updatedAt')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('points')
            ->add('company')
            ->add('title')
            ->add('description')
            ->add('link')  
            ->add('file')
            ->add('createdAt')
            ->add('updatedAt')
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
