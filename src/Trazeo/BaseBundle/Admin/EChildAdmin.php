<?php

namespace Trazeo\BaseBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class EChildAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('nick', null, array('label' => 'list.label_nick'))
            ->add('userextendchilds', null, array('label' => 'list.label_userextendchilds'))
            ->add('dateBirth', null, array('label' => 'list.label_date_birth'))
            ->add('visibility', null, array('label' => 'show.label_visibility'))
            ->add('gender', null, array('label' => 'show.label_gender'))
            ->add('selected', null, array('label' => 'show.label_selected'))
            ->add('ride', null, array('label' => 'show.label_ride'))
            ->add('groups', null, array('label' => 'show.label_groups'))
            ->add('inviteChild', null, array('label' => 'show.label_invite_child'))
            ->add('medals', null, array('label' => 'list.label_medals'))
            ->add('createdAt', null, array('label' => 'show.label_created_at'))
            ->add('updatedAt', null, array('label' => 'show.label_updated_at'));
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id')
            ->add('nick', null, array('label' => 'list.label_nick'))
            ->add('userextendchilds', null, array('label' => 'list.label_userextendchilds'))
            ->add('dateBirth', null, array('label' => 'list.label_date_birth'))
            ->add('visibility', null, array('label' => 'show.label_visibility'))
            ->add('gender', null, array('label' => 'show.label_gender'))
            ->add('selected', null, array('label' => 'show.label_selected'))
            ->add('ride', null, array('label' => 'show.label_ride'))
            ->add('groups', null, array('label' => 'show.label_groups'))
            ->add('inviteChild', null, array('label' => 'show.label_invite_child'))
            ->add('scholl', null, array('label' => 'list.label_scholl'))
            ->add('createdAt', null, array('label' => 'show.label_created_at'))
            ->add('_action', 'actions', array(
                'label' => 'list.label_action',
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ));
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('nick', null, array('label' => 'list.label_nick'))
            ->add('dateBirth', null, array('label' => 'list.label_date_birth'))
            //->add('visibility')
            ->add('gender', null, array('label' => 'show.label_gender'))
            ->add('selected', null, array('label' => 'show.label_selected'))
            ->add('ride', null, array('label' => 'show.label_ride'))
            ->add('inviteChild', null, array('label' => 'show.label_invite_child'))
            ->add('scholl', null, array('label' => 'list.label_scholl'))
            ->add('medals', 'sonata_type_model', array(
                'multiple' => true, 'by_reference' => false,
                'label' => 'list.label_medals'
                ))
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('nick', null, array('label' => 'list.label_nick'))
            ->add('dateBirth')
            ->add('visibility')
            ->add('gender', null, array('label' => 'show.label_gender'))
            ->add('selected', null, array('label' => 'show.label_selected'))
            ->add('scholl', null, array('label' => 'list.label_scholl'))
            ->add('medals', null, array('label' => 'list.label_medals'))
        ;
    }
}
