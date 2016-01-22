<?php

namespace Trazeo\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Trazeo\BaseBundle\Entity\EGroupRepository;

class GroupType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', 'entity', array(
                'class' => 'JJsGeonamesBundle:Country',
                'attr' => array('class' => 'chosen-select'),
                'required' => false, //Para permitir hacer submit con una opción 'vacía'
                'empty_value' => 'España', //Valor que aparece por defecto si no tiene un país asignado
                'empty_data' => $options['attr']['default'],
                'property' => 'name'))

            ->add('name', null, array(
                'attr' => array(
                    'placeholder' => 'Groups.name',
                    'data-toggle' => 'popover',
                    'data-placement' => 'right',
                    'data-content' => $options['attr']['Groups.help.name']
                ),'required' => true))
            /*->add('city', 'entity', array(
                'class' => 'JJsGeonamesBundle:City',
                'attr' => array(
                    'class' => 'chosen-select',
                    'data-toggle' => 'popover',
                    'data-placement' => 'right',
                    'data-content' => $options['attr']['Groups.help.city']
                ),
                'required' => false, //Para permitir hacer submit con una opción 'vacía'
                'empty_value' => 'España', //Valor que aparece por defecto si no tiene un país asignado
                'property' => 'city')
            )*/

            ->add('visibility', 'choice', array(
                'choices'   => array(0 => 'Groups.visibility.public', 1 => 'Groups.visibility.private',2 => 'Groups.visibility.hidden'),
                'attr' => array(
                    'placeholder' => 'Groups.visibility',
                    'data-toggle' => 'popover',
                    'data-placement' => 'right',
                    'data-content' => $options['attr']['Groups.help.name2']
                )))
            ->add('school1', 'datalist', ['choices' => $options['schools']]);
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Trazeo\BaseBundle\Entity\EGroup',
            'schools'         => [],
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // For Symfony 2.0:
        // $view->set('base_path', $form->getAttribute('base_path'));

        // For Symfony 2.1 and higher:
        $view->vars['schools'] = $options['schools'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trazeo_basebundle_group';
    }
}
