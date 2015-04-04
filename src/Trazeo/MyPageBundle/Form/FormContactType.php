<?php

namespace Trazeo\MyPageBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Trazeo\BaseBundle\Service\Helper;

class FormContactType extends AbstractType
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
        $builder->add('group', 'entity', array(
                'label' => "Si lo desea, indique la Ruta sobre la que quiere efectuar su consulta",
                'required' => false,
                'class' => 'TrazeoBaseBundle:EGroup',
                'attr' => array(
                    'placeholder' => "Si lo desea, indique la Ruta sobre la que quiere efectuar su consulta"
                ),
                'multiple' => false,
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
        $builder->add('name',
            'text',
            array(
                'label' => "Tu nombre",
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Tu nombre'
                )
            )
        );
        $builder->add('email',
            'email',
            array(
                'label' => "Tu correo electrónico",
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Tu correo electrónico'
                )
            )
        );
        $builder->add('message',
            'textarea',
            array(
                'label' => "Tu mensaje",
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Tu mensaje'
                )
            )
        );
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return "FormContact";
    }
}