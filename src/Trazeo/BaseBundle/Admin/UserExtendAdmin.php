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
        ->add('adminGroups', null, array('required' => false))
        ->add('adminRoutes', null, array('required' => false))
        ->add('reports')
        ->add('childs', null, array('required' => false))
        ->add('city')
        ->add('tutorial', null, array('required' => false))
        ->add('tutorialMap', null, array('required' => false))
        ->add('access')
        ->add('inviteGroup')
        ->add('inviteGroupSender')
        ->add('inviteChild')
        ->add('inviteChildSender')
        ->add('useLike')
        ->add('name')
        ->add('nick')
        ->add('points')
        ->add('spendedPoints')
        ->add('mobile')
    ;

  }
 
  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
        ->add('user')
        ->add('groups')
        ->add('adminGroups')
        ->add('adminRoutes')
        ->add('reports')
        ->add('childs')
        ->add('city')
        ->add('tutorial')
        ->add('tutorialMap')
        ->add('access')
        ->add('inviteGroup')
        ->add('inviteGroupSender')
        ->add('inviteChild')
        ->add('inviteChildSender')
        ->add('useLike')
        ->add('name')
        ->add('nick')
        ->add('points')
        ->add('spendedPoints')
        ->add('mobile')
    ;
  }
 
  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
      ->addIdentifier('id')
      ->add('user')
      ->add('groups')
      ->add('adminGroups')
      ->add('adminRoutes')
      ->add('reports')
      ->add('childs')
      ->add('city')
      ->add('country')
      ->add('tutorial')
      ->add('tutorialMap')
        ->add('access')
        ->add('inviteGroup')
        ->add('inviteGroupSender')
        ->add('inviteChild')
        ->add('inviteChildSender')
      ->add('useLike')
        ->add('name')
        ->add('nick')
        ->add('points')
        ->add('spendedPoints')
        ->add('mobile')
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