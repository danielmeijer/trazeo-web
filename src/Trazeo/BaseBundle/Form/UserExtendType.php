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
            ->add('nick')
            ->add('user')
            ->add('groups')
            ->add('children')
             ->add('country', 'entity', array(
            		'class' => 'JJsGeonamesBundle:Country',
            		'attr' => array('class' => 'chosen-select'),
            		'property' => 'name'))
            ->add('city', 'entity', array(
            		'class' => 'JJsGeonamesBundle:City',
            		'attr' => array('class' => 'chosen-select'),
            		'property' => 'name'))
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
