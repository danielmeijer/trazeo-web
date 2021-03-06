<?php

namespace Trazeo\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserExtendType extends AbstractType
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
        					'placeholder' => 'Userextend.help.nick',
        					'data-toggle' => 'popover',
        					'data-placement' => 'right',
        					'data-content' => $options['attr']['Userextend.help.nick']
        					),
            		'required' => false
        			)
        		)
            ->add('mobile', 'number', array(
                    'attr' => array(
                            'placeholder' => 'Userextend.help.mobile',
                            'data-toggle' => 'popover',
                            'data-placement' => 'right',
                            'data-content' => $options['attr']['Userextend.help.mobile']
                            ),
                    'required' => false
                    )
                )
             ->add('country', 'entity', array(
            		'class' => 'JJsGeonamesBundle:Country',
            		'attr' => array('class' => 'chosen-select'),
             		'required' => false, //Para permitir hacer submit con una opción 'vacía'
             		'empty_value' => 'España', //Valor que aparece por defecto si no tiene un país asignado
             		'empty_data' => $options['attr']['default'],
            		'property' => 'name'))
            
             /**->add('city', 'entity', array(
            		'class' => 'JJsGeonamesBundle:City',
            		'attr' => array('class' => 'chosen-select'),
            		'property' => 'name'))**/
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Trazeo\BaseBundle\Entity\UserExtend'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trazeo_basebundle_userextend';
    }
}
