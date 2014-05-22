<?php
namespace Trazeo\BaseBundle\Admin;
 
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
 
class UserExtendAdmin extends Admin
{
  protected $translationDomain = 'TrazeoBaseBundleAdmin';

  protected function configureFormFields(FormMapper $formMapper)
  {
    $formMapper
        ->add('user')
        ->add('groups', null, array('required' => false))
        ->add('adminRoutes')
        ->add('children', null, array('required' => false))
        ->add('nick')
    ;
  }
 
  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
    	->add('nick')
    	->add('groups')
    ;
  }
 
  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
      ->addIdentifier('id')
      ->add('user')
      ->add('groups')
      ->add('adminRoutes')
      ->add('children')
      ->add('nick')
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