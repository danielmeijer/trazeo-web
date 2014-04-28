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
            		'empty_data'  => 263, //Dato que se registra en la base de datos si se hace un commit con esta selección 'vacía'
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
}
