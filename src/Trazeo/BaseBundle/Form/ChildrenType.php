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
            ->add('nick')
    		->add('date_birth', 'date', array(
    			'widget' => 'single_text',
    			'attr' => array('class' => 'inputDate'),
    			'format' => 'dd/MM/yyyy'))
            ->add('visibility', 'choice', array(
            		'choices'   => array('enabled' => 'Enabled', 'disabled' => 'Disabled')))
            ->add('sex')
            ->add('groups')
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
