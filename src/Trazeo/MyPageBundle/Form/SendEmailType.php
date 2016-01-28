<?php
namespace Trazeo\MyPageBundle\Form;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Trazeo\BaseBundle\Service\Helper;

class SendEmailType extends AbstractType
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
        $builder->add('subject',
            'text',
            array(
                'label' => "Asunto del Email",
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Indique el mensaje del Email'
                )
            ),
            null
        );
        $builder->add('body', 'ckeditor',
            array(
                'label' => "Cuerpo del Email"
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
        return "SendEmail";
    }
}