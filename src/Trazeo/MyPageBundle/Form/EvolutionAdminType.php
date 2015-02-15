<?php

namespace Trazeo\MyPageBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Trazeo\BaseBundle\Service\Helper;

class EvolutionAdminType extends AbstractType
{
    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        //ldd($page);

        $builder->add('group', 'entity', array(
                'label' => "Grupos",
                'required' => false,
                'class' => 'TrazeoBaseBundle:EGroup',
                'multiple' => true,
                'query_builder' => function(EntityRepository $er) {
                    /** @var Helper $helper */
                    $helper = $this->container->get('trazeo_base_helper');
                    $page = $helper->getPageBySubdomain();
                    return $er->createQueryBuilder('u')
                        ->where('u.page = :page')
                        ->setParameter('page', $page)
                        ->orderBy('u.name', 'ASC');
                },)
        );
        $builder->add('date_from',
            $options['field_type'],
            array(
                'label' => "Fecha (desde)",
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Fecha'
                )
            ),
            $options['field_options']
        );
        $builder->add('date_to',
            $options['field_type'],
            array(
                'label' => "Fecha (hasta)",
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Fecha'
                )
            ),
            $options['field_options']
        );
        $builder->add('age_from',
            'integer',
            array(
                'label' => "Edad (desde)",
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Edad'
                )
            ),
            $options['field_options']
        );
        $builder->add('age_to',
            'integer',
            array(
                'label' => "Edad (hasta)",
                'required' => false,
                'attr' => array(
                    'placeholder' => 'Edad'
                )
            ),
            $options['field_options']
        );
        $builder->add('mode',
            'choice',
            array(
                'label' => 'Agrupación de datos',
                'choices' => array(
                    'month' => 'por Mes',
                    'week' => 'por Semana',
                    'day' => 'por Días'
                )
            ),
            $options['field_options']
        );

        // Sexo

        // Centro Educativo
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'field_options'    => array(),
            'field_type'       => 'sonata_type_date_picker',
        ));
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "EvolutionAdmin";
    }
}