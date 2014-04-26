<?php

namespace Trazeo\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChildType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nick', null, array(
        			'attr' => array(
        					'placeholder' => 'Children.nick',
        					'data-toggle' => 'popover',
        					'data-placement' => 'right',
        					'data-content' => 'Children.help.nick'
        					),'required' => true
        			)
        		)
          /*  ->add('userextendchilds', null, array(
        			'attr' => array(
        					'placeholder' => 'Route.name',
        					'data-toggle' => 'popover',
        					'data-placement' => 'right',
        					'data-content' => 'Children.help.userextend'
        					)))
            ->add('groups', null, array(
        			'attr' => array(
        					'placeholder' => 'Route.name',
        					'data-toggle' => 'popover',
        					'data-placement' => 'right',
        					'data-content' => 'Children.help.groups'
        					)))
    		*/->add('date_birth', 'date', array(
    			'widget' => 'single_text',
    			'attr' => array(
    					'class' => 'inputDate', 
    					'placeholder' => 'Children.date',
    					'data-toggle' => 'popover',
    					'data-placement' => 'right',
    					'data-content' => 'Children.help.dateBirth'),
    			'format' => 'dd/MM/yyyy','required' => true))
    			
            ->add('visibility', 'choice', array(
            		'choices'   => array(1 => 'Children.visibility.enabled', 0 => 'Children.visibility.disabled'),
            		'attr' => array(
            				'placeholder' => 'Children.sex',
            				'data-toggle' => 'popover',
            				'data-placement' => 'right',
            				'data-content' => 'Children.help.visibility'
            		)))
            		
            ->add('gender', 'choice', array(
            		'choices' => array('boy' => 'Children.sex.boy', 'girl' => 'Children.sex.girl'),
        			'attr' => array(
        					'placeholder' => 'Children.sex',
        					'data-toggle' => 'popover',
        					'data-placement' => 'right',
        					'data-content' => 'Children.help.sex'
        					)
        			)
        		)
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Trazeo\BaseBundle\Entity\EChild'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trazeo_basebundle_child';
    }
}
