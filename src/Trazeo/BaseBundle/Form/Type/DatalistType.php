<?php
// src/Acme/Form/Type/DatalistType
namespace Trazeo\BaseBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DatalistType extends AbstractType
{
    public function getParent()
    {
        return 'text';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setRequired(['choices']);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['choices']= $options['choices'];
    }

    public function getName()
    {
        return 'datalist';
    }
}