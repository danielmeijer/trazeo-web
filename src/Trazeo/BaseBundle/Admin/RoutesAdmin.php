<?php
namespace Trazeo\BaseBundle\Admin;
 
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
 
class RoutesAdmin extends Admin
{
  protected $translationDomain = 'TrazeoBaseBundleAdmin';

  protected function configureFormFields(FormMapper $formMapper)
  {
    $formMapper
        ->add('groups', null, array('required' => false))
        ->add('admin')
        ->add('country', 'entity', array(
      		'property' => 'name',
      		'class' => 'JJs\Bundle\GeonamesBundle\Entity\Country'
      		))
        /*->add('city', 'entity', array(
      		'property' => 'name',
      		'class' => 'JJs\Bundle\GeonamesBundle\Entity\City'
      		)) TODO: No se cargan */ 
        ->add('name')
    ;
  }
 
  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
      ->add('groups')
      ->add('admin')
      ->add('name')
    ;
  }
 
  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
      ->addIdentifier('id')
      ->add('groups')
      ->add('admin')
      ->add('name');
  }
 
  public function validate(ErrorElement $errorElement, $object)
  {
    /*$errorElement
      ->with('class')
      ->assertMaxLength(array('limit' => 3))
      ->end()
    ;*/
  }
}
?>
