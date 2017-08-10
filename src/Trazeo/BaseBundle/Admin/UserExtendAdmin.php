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
        ->add('user', null, array('label' => 'list.label_user_username'))
        ->add('groups', null, array('required' => false, 'label' => 'show.label_groups'))
        ->add('adminGroups', null, array('required' => false, 'label' => 'list.label_admin_group'))
        ->add('adminRoutes', null, array('required' => false, 'label' => 'list.label_admin_route'))
        ->add('reports', null, array('label' => 'show.label_reports'))
        ->add('childs', null, array('required' => false, 'label' => 'list.label_childs'))
        ->add('tutorial', null, array('required' => false, 'label' => 'list.label_tutorial'))
        ->add('tutorialMap', null, array('required' => false, 'label' => 'list.label_tutorial_map'))
        ->add('access', array('label' => 'list.label_access'))
        ->add('inviteGroup', null, array('label' => 'show.label_invite_group'))
        ->add('inviteGroupSender', null, array('label' => 'show.label_invite_group_sender'))
        ->add('inviteChild', null, array('label' => 'show.label_invite_child'))
        ->add('inviteChildSender', null, array('label' => 'show.label_invite_child_sender'))
        ->add('useLike')
        ->add('name', null, array('label' => 'list.label_name'))
        ->add('nick', null, array('label' => 'list.label_nick'))
        ->add('points', null, array('label' => 'list.label_points'))
        ->add('spendedPoints', null, array('label' => 'list.label_points_spend'))
        ->add('mobile', null, array('label' => 'list.label_mobile'))
    ;

  }

  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
        ->add('user', null, array('label' => 'list.label_user_username'))
        ->add('groups', null, array('label' => 'show.label_groups'))
        ->add('adminGroups', null, array('label' => 'list.label_admin_group'))
        ->add('adminRoutes', null, array('label' => 'list.label_admin_route'))
        ->add('reports', null, array('label' => 'show.label_reports'))
        ->add('childs', null, array('label' => 'list.label_childs'))
        ->add('tutorial', null, array('label' => 'list.label_tutorial'))
        ->add('tutorialMap', null, array('label' => 'list.label_tutorial_map'))
        ->add('access', array('label' => 'list.label_access'))
        ->add('inviteGroup', null, array('label' => 'show.label_invite_group'))
        ->add('inviteGroupSender', null, array('label' => 'show.label_invite_group_sender'))
        ->add('inviteChild', null, array('label' => 'show.label_invite_child'))
        ->add('inviteChildSender', null, array('label' => 'show.label_invite_child_sender'))
        ->add('useLike')
        ->add('name', null, array('label' => 'list.label_name'))
        ->add('nick', null, array('label' => 'list.label_nick'))
        ->add('points', null, array('label' => 'list.label_points'))
        ->add('spendedPoints', null, array('label' => 'list.label_points_spend'))
        ->add('mobile', null, array('label' => 'list.label_mobile'))
        ->add('city.nameUtf8', null, array('label' => 'list.label_city_name'))
    ;
  }

  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
      ->addIdentifier('id')
      ->add('user', null, array('label' => 'list.label_user_username'))
      ->add('groups', null, array('label' => 'show.label_groups'))
      ->add('adminGroups', null, array('label' => 'list.label_admin_group'))
      ->add('adminRoutes', null, array('label' => 'list.label_admin_route'))
      ->add('reports', null, array('label' => 'show.label_reports'))
      ->add('childs', null, array('label' => 'list.label_childs'))
      ->add('tutorial', null, array('label' => 'list.label_tutorial'))
      ->add('tutorialMap', null, array('label' => 'list.label_tutorial_map'))
        ->add('access', array('label' => 'list.label_access'))
        ->add('inviteGroup', null, array('label' => 'show.label_invite_group'))
        ->add('inviteGroupSender', null, array('label' => 'show.label_invite_group_sender'))
        ->add('inviteChild', null, array('label' => 'show.label_invite_child'))
        ->add('inviteChildSender', null, array('label' => 'show.label_invite_child_sender'))
      ->add('useLike')
        ->add('name', null, array('label' => 'list.label_user_username'))
        ->add('nick', null, array('label' => 'list.label_nick'))
        ->add('points', null, array('label' => 'list.label_points'))
        ->add('spendedPoints', null, array('label' => 'list.label_points_spend'))
        ->add('mobile', null, array('label' => 'list.label_mobile'))
        ->add('city.nameUtf8', null, array('label' => 'list.label_city_name'))
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
      ->add('nick', null, array('label' => 'list.label_nick'))
      ->add('points', null, array('label' => 'list.label_points'))
      ->add('spendedPoints', null, array('label' => 'list.label_points_spend'))
      ->add('mobile', null, array('label' => 'list.label_mobile'))
      ;

    }

}
?>