<?php

namespace Trazeo\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class GroupsType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', null, array(
        		'attr' => array('placeholder' => 'Groups.name')))
        ->add('routes',null , array(
        		'data' => array('empty_value' => 'Ruotes.Name')))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Trazeo\BaseBundle\Entity\Groups'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trazeo_basebundle_groups';
    }
}
