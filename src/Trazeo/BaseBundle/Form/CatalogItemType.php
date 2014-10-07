<?php

namespace Trazeo\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CatalogItemType extends AbstractType
{
	 /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('points', null, array(
        			'attr' => array(
        					'placeholder' => 'CatalogItem.points',
        					'data-toggle' => 'popover',
        					'data-placement' => 'right',
        					'data-content' => $options['attr']['CatalogItem.help.points']
        					),'required' => true
        			)
        		)
            ->add('company', null, array(
        			'attr' => array(
        					'placeholder' => 'CatalogItemcompany',
        					'data-toggle' => 'popover',
        					'data-placement' => 'right',
        					'data-content' => $options['attr']['CatalogItem.help.company']
        					)))
            ->add('title', null, array(
        			'attr' => array(
        					'placeholder' => 'CatalogItem.title',
        					'data-toggle' => 'popover',
        					'data-placement' => 'right',
        					'data-content' => $options['attr']['CatalogItem.help.title']
        					)))
            ->add('description', null, array(
        			'attr' => array(
        					'placeholder' => 'CatalogItem.description',
        					'data-toggle' => 'popover',
        					'data-placement' => 'right',
        					'data-content' => $options['attr']['CatalogItem.help.description']
        					)))
            ->add('link', null, array(
        			'attr' => array(
        					'placeholder' => 'CatalogItem.link',
        					'data-toggle' => 'popover',
        					'data-placement' => 'right',
        					'data-content' => $options['attr']['CatalogItem.help.link']
        					)))

        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Trazeo\BaseBundle\Entity\ECatalogItem'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'trazeo_basebundle_catalogitem';
    }
}