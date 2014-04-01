<?php
namespace Trazeo\BaseBundle\Admin;
 
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
 
class GroupAdmin extends Admin
{
  protected $translationDomain = 'TrazeoBaseBundleAdmin';

  protected function configureFormFields(FormMapper $formMapper)
  {
    $formMapper
        ->add('userextendgroups', null, array('required' => false))
        ->add('admin')
        ->add('childs', null, array('required' => false))
        ->add('routes', null, array('required' => false))
        ->add('name')
    ;
  }
 
  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
      ->add('routes')
      ->add('name')
    ;
  }
 
  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
      ->addIdentifier('id')
      ->add('userextendgroups')
      ->add('admin')
      ->add('childs')
      ->add('routes')
      ->add('name')
    ;
  }
 
  public function validate(ErrorElement $errorElement, $object)
  {
    /*$errorElement
      ->with('text')
      ->assertMaxLength(array('limit' => 3))
      ->end()
    ;*/
  }
}
?>
