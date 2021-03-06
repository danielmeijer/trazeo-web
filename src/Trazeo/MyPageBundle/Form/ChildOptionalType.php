<?php

namespace Trazeo\MyPageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChildOptionalType extends AbstractType
{
    private $name = 'trazeo_basebundle_child';

    public function getName() {
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

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
        					'data-content' => $options['attr']['Children.help.nick']
        					),'required' => false
        			)
        		)
            ->add('scholl', null, array(
        			'attr' => array(
        					'placeholder' => 'Children.scholl',
        					'data-toggle' => 'popover',
        					'data-placement' => 'right',
        					'data-content' => $options['attr']['Children.help.scholl']
        					),'required' => false))
            /*->add('groups', null, array(
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
    					'data-content' => $options['attr']['Children.help.datebirth']
    			),
    			'format' => 'dd/MM/yyyy','required' => false))

           /* ->add('visibility', 'choice', array(
            		'choices'   => array(1 => 'Children.visibility.enabled', 0 => 'Children.visibility.disabled'),
            		'attr' => array(
            				'placeholder' => 'Children.sex',
            				'data-toggle' => 'popover',
            				'data-placement' => 'right',
            				'data-content' => $options['attr']['Children.help.visibility']
            		)))*/

            ->add('gender', 'choice', array(
            		'choices' => array('boy' => 'Children.gender.boy', 'girl' => 'Children.gender.girl'),
        			'attr' => array(
        					'placeholder' => 'Children.gender',
        					'data-toggle' => 'popover',
        					'data-placement' => 'right',
        					'data-content' => $options['attr']['Children.help.gender']
        					),'required' => false
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
}
