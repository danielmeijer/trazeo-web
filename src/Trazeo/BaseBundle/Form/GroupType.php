<?php

namespace Trazeo\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GroupType extends AbstractType
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
        				'placeholder' => 'Groups.name',
        				'data-toggle' => 'popover',
        				'data-placement' => 'right',
        				'data-content' => 'Groups.help.name'
        		),'required' => true))
        
        ->add('route', null, array(
        		'attr' => array(
        				'placeholder' => 'Groups.name',
        				'data-toggle' => 'popover',
        				'data-placement' => 'right',
        				'data-content' => 'Groups.help.route'
        		)),
        		array('empty_value' => 'Trans.select.option'));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Trazeo\BaseBundle\Entity\EGroup'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trazeo_basebundle_group';
    }
}
