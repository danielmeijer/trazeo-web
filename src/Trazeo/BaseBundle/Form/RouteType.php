<?php

namespace Trazeo\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RouteType extends AbstractType
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
        					'data-content' => $options['attr']['Route.help.name']
        					),'required' => true))
        	->add('description', null, array(
        						'attr' => array(
        						'placeholder' => 'Route.description',
        						'data-toggle' => 'popover',
        						'data-placement' => 'right',
        						'data-content' => $options['attr']['Route.help.description']
        					),'required' => false))
            ->add('country', 'entity', array(
            		'class' => 'JJsGeonamesBundle:Country',
            		'attr' => array(
            				'class' => 'chosen-select',
            				'data-toggle' => 'popover',
            				'data-placement' => 'right',
            				'data-content' => $options['attr']['Route.help.country']
            		),
            		'required' => false, //Para permitir hacer submit con una opción 'vacía'
            		'empty_value' => 'España', //Valor que aparece por defecto si no tiene un país asignado
            		'empty_data' => $options['attr']['default'],
            		'property' => 'name'
           	));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Trazeo\BaseBundle\Entity\ERoute'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trazeo_basebundle_route';
    }
    
    /**
     * @return string
     */
    public function getDescription()
    {
    	return 'trazeo_basebundle_route';
    }
}
