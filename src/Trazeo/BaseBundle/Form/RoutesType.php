<?php

namespace Trazeo\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RoutesType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {    	
        $builder
            ->add('name', null, array(
        			'attr' => array(
        					'placeholder' => 'Route.name',
        					'data-toggle' => 'popover',
        					'data-placement' => 'right',
        					'data-content' => 'Route.help.name'
        					),'required' => true))
            ->add('country', 'entity', array(
            		'class' => 'JJsGeonamesBundle:Country',
            		'attr' => array(
            				'class' => 'chosen-select',
            				'data-toggle' => 'popover',
            				'data-placement' => 'right',
            				'data-content' => 'Children.help.country'
            		),
            		'property' => 'name'
           	));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Trazeo\BaseBundle\Entity\Routes'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trazeo_basebundle_routes';
    }
}
