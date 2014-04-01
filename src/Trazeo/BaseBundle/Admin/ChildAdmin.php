<?php
namespace Trazeo\BaseBundle\Admin;
 
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
 
class ChildAdmin extends Admin
{
  protected $translationDomain = 'TrazeoBaseBundleAdmin';

  protected function configureFormFields(FormMapper $formMapper)
  {
    $formMapper
        ->add('userextendchilds',null, array('required' => false))
        ->add('groups', null, array('required' => false))
        ->add('nick')
        ->add('dateBirth')
        ->add('visibility','choice', array('choices' => array(
       			"0" => "No",
       			"1" => "Si"
       		)))
        ->add('gender','choice', array('choices' => array(
       			"H" => "Niño",
       			"M" => "Niña"
       		)))
    ;
  }
 
  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
      ->add('userextendchilds')->add('groups')->add('nick')->add('dateBirth')->add('visibility')->add('gender')
    ;
  }
 
  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
      ->addIdentifier('id')->add('userextendchilds')->add('groups')->add('nick')->add('dateBirth')->add('visibility')->add('gender')
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
