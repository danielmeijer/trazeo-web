<?php
namespace Trazeo\MyPageBundle\Admin;
 
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
 
class MenuAdmin extends Admin
{

  protected function configureFormFields(FormMapper $formMapper)
  {
    $formMapper
        ->add('title', null, array('label' => 'list.label_title'))
        ->add('page', null, array('label' => 'form.label_page'))
        ->add('modules', null, array('label' => 'list.label_modules'))
        ->add('position', null, array('label' => 'list.label_position'))
        ->add('link', null, array('label' => 'list.label_link'))
        ->add('color_back')
        ->add('color_front')
    ;
  }
 
  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
        ->add('title', null, array('label' => 'list.label_title'))
        ->add('page', null, array('label' => 'form.label_page'))
        ->add('modules', null, array('label' => 'list.label_modules'))
        ->add('position', null, array('label' => 'list.label_position'))
        ->add('link', null, array('label' => 'list.label_link'))
    ;
  }
 
  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
        ->addIdentifier('id')
        ->add('title', null, array('label' => 'list.label_title'))
        ->add('page', null, array('label' => 'form.label_page'))
        ->add('modules', null, array('label' => 'list.label_modules'))
        ->add('position', null, array('label' => 'list.label_position'))
        ->add('link', null, array('label' => 'list.label_link'))
    ;
  }
}
?>