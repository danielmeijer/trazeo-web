<?php

namespace Trazeo\MyPageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserExtendCustomType extends AbstractType
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
                        'placeholder' => 'Nombre y Apellidos',
                        'data-toggle' => 'popover',
                        'data-placement' => 'right'
                    ),
                    'required' => false
                )
            )
            ->add('mobile', 'number', array(
                    'attr' => array(
                        'placeholder' => 'Userextend.help.mobile',
                        'data-toggle' => 'popover',
                        'data-placement' => 'right'
                    ),
                    'required' => false
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
            'data_class' => 'Trazeo\BaseBundle\Entity\UserExtend'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trazeo_basebundle_userextendcustom';
    }
}
