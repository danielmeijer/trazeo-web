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
        ->add('adminRoutes', null, array('required' => false))
        ->add('childs', null, array('required' => false))
        ->add('nick')
        ->add('tutorial', null, array('required' => false))
        ->add('tutorialMap', null, array('required' => false))
        ->add('useLike')
    ;
  }
 
  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
    	->add('nick')
    	->add('groups')
    	->add('tutorial')
    	->add('tutorialMap')    	
    	->add('useLike')
    ;
  }
 
  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
      ->addIdentifier('id')
      ->add('user')
      ->add('groups')
      ->add('adminRoutes')
      ->add('childs')
      ->add('nick')      
      ->add('useLike')
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