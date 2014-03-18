<?php

namespace Trazeo\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChildrenType extends AbstractType
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
        					'placeholder' => 'Children.nick'
        					)
        			)
        		)
        	->add('userextendchildren')
        	->add('groups')
    		->add('date_birth', 'date', array(
    			'widget' => 'single_text',
    			'attr' => array('class' => 'inputDate', 'placeholder' => 'Children.date'),
    			'format' => 'dd/MM/yyyy'))
            ->add('visibility', 'choice', array(
            		'choices'   => array(1 => 'Enabled', 0 => 'Disabled')))
            ->add('sex', 'choice', array(
            		'choices' => array('boy' => 'Children.sex.boy', 'girl' => 'Children.sex.girl'),
        			'attr' => array(
        					'placeholder' => 'Children.sex'
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
            'data_class' => 'Trazeo\BaseBundle\Entity\Children'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trazeo_basebundle_children';
    }
}
