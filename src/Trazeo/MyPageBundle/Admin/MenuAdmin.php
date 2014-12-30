<?php
namespace Trazeo\MyPageBundle\Admin;
 
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
 
class MenuAdmin extends Admin
{
  protected $translationDomain = 'TrazeoBaseBundleAdmin';

  protected function configureFormFields(FormMapper $formMapper)
  {
    $formMapper
        ->add('title')
        ->add('page')
        ->add('modules')
        ->add('position')
        ->add('link')
        ->add('color_back')
        ->add('color_front')
    ;
  }
 
  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
        ->add('title')
        ->add('page')
        ->add('modules')
        ->add('position')
        ->add('link')
    ;
  }
 
  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
        ->addIdentifier('id')
        ->add('title')
        ->add('page')
        ->add('modules')
        ->add('position')
        ->add('link')
    ;
  }
}
?>