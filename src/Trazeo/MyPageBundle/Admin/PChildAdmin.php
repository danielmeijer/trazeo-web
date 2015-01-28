<?php

namespace Trazeo\MyPageBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Trazeo\BaseBundle\Entity\EChild;

class PChildAdmin extends Admin
{
    public function getBatchActions()
    {
        // retrieve the default batch actions (currently only delete)
        $actions = parent::getBatchActions();

        /**
        if (
            $this->hasRoute('edit') && $this->isGranted('EDIT') &&
            $this->hasRoute('delete') && $this->isGranted('DELETE')
        ) {**/
            $actions['merge'] = array(
                'label' => $this->trans('action_graph', array(), 'SonataAdminBundle'),
                'ask_confirmation' => false
            );

        //}

        return $actions;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('groups')
            ->add('createdAt', 'doctrine_orm_datetime_range', array('format' => 'MM/dd/yyyy'), 'DateRangePicker')
            ->add('scholl')
            // EDAD
            //->add('createdAt', 'doctrine_orm_datetime_range', array(), 'sonata_type_datetime_picker')
            ->add('gender', 'doctrine_orm_choice', [],
                'choice',
                [
                    'choices' => array(
                        EChild::GENDER_BOY => "Niño",
                        Echild::GENDER_GIRL => "Niña"
                    )
                ]
            );

        /**
            $datagridMapper
                ->add('id')
                ->add('nick')
                ->add('userextendchilds')
                ->add('dateBirth')
                ->add('visibility')
                ->add('gender')
                ->add('selected')
                ->add('ride')
                ->add('groups')
                ->add('inviteChild')
                ->add('createdAt')
                ->add('updatedAt');
        }
         * **/
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('nick')
            ->add('scholl')
            ->add('dateBirth')
            ->add('createdAt');

        /**
            $listMapper
                ->addIdentifier('id')
                ->add('nick')
                ->add('userextendchilds')
                ->add('dateBirth')
                ->add('visibility')
                ->add('gender')
                ->add('selected')
                ->add('_action', 'actions', array(
                    'actions' => array(
                        'show' => array(),
                        'edit' => array(),
                        'delete' => array(),
                    )
                ))
                ->add('ride')
                ->add('groups')
                ->add('inviteChild')
                ->add('createdAt')
                ->add('updatedAt');
        }
         **/
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('nick')
            ->add('dateBirth', 'sonata_type_datetime_picker')
            ->add('visibility')
            ->add('gender')
            ->add('selected')
            ->add('ride')
            ->add('inviteChild')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('nick')
            ->add('dateBirth')
            ->add('visibility')
            ->add('gender')
            ->add('selected')
        ;
    }
}
