<?php
namespace Trazeo\BaseBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

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
        ->add('city.nameUtf8')
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
        ->add('city.nameUtf8')
        ->add('_action', 'actions', array(
            'actions' => array(
                'show' => array(),
                'edit' => array(),
                'delete' => array(),
            )
        ))
    ;
  }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        // Here we set the fields of the ShowMapper variable, $showMapper (but this can be called anything)
        $showMapper
      ->add('nick')
      ->add('points')
      ->add('spendedPoints')
      ->add('mobile')
      ;

    }

}
?>