<?php

namespace Trazeo\MyPageBundle\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Model\BlockInterface;
use Symfony\Component\HttpFoundation\Response;

use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

use Sonata\BlockBundle\Block\BaseBlockService;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class StatBlockService extends BaseBlockService
{
    public function getName()
    {
        return 'Estadísticas del sistema';
    }

    public function getDefaultSettings()
    {
        return array();
    }


    public function buildEditForm(FormMapper $formMapper, BlockInterface $block)
    {

    }

    public function setDefaultSettings(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'template' => 'TrazeoMyPageBundle:Block:Stat\resume.html.twig',
            'title' => "Estadísticas del sistema",
            'ttl' => 0
        ));
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        // merge settings
        $settings = array_merge($this->getDefaultSettings(), $blockContext->getSettings());

        return $this->renderResponse($blockContext->getTemplate(), array(
            'block'     => $blockContext->getBlock(),
            'settings'  => $settings
        ), $response);
    }
}