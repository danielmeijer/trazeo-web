<?php

namespace Trazeo\BaseBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sopinet\Bundle\SimplePointBundle\ORM\Type\SimplePoint;
use Trazeo\BaseBundle\Form\DataTransformer\TextToPointTransformer;

class EPointsAdmin extends Admin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('pickup', null, array('label' => 'list.label_pickup'))
            ->add('description', null, array('label' => 'list.label_description'))
            ->add('location', null, array('label' => 'show.label_location'))
            ->add('route', null, array('label' => 'show.label_route'))
            ->add('createdAt', null, array('label' => 'show.label_created_at'))
            ->add('updatedAt', null, array('label' => 'show.label_updated_at'))
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('pickup', null, array('label' => 'list.label_pickup'))
            ->add('description', null, array('label' => 'list.label_description'))
            ->add('location', null, array('label' => 'show.label_location'))
            ->add('_action', 'actions', array(
                'label' => 'list.label_action',
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
            ->add('route', null, array('label' => 'show.label_route'))
            ->add('createdAt', null, array('label' => 'show.label_created_at'))
            ->add('updatedAt', null, array('label' => 'show.label_updated_at'))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $transformer = new TextToPointTransformer();

        $formMapper
            ->add('pickup', null, array('label' => 'list.label_pickup'))
            ->add('description', null, array('label' => 'list.label_description'))
            ->add('location', null, array('label' => 'show.label_location'))
            ->get('location')->addModelTransformer($transformer)
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('pickup', null, array('label' => 'list.label_pickup'))
            ->add('description', null, array('label' => 'list.label_description'))
            ->add('location', null, array('label' => 'show.label_location'))
        ;
    }
}
