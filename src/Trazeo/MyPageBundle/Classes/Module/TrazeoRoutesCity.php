<?php
namespace Trazeo\MyPageBundle\Classes\Module;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use Trazeo\BaseBundle\Service\Helper;
use Trazeo\MyPageBundle\Classes\ModuleAbstract;
use Trazeo\MyPageBundle\Entity\Module;

class TrazeoRoutesCity extends ModuleAbstract {
    function prepareFront($container, Module $module = null) {
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();
        $repositoryGroup = $em->getRepository('TrazeoBaseBundle:EGroup');

        $cities = $module->getContentArray();

        $arrayGroups = array();
        foreach($cities as $city) {
            $groups_from_city = $repositoryGroup->findBy(array('city' => $city));
            $arrayGroups = array_merge($arrayGroups, $groups_from_city);
        }

        return $arrayGroups;
    }

    public function getAdminDescription(Module $module) {
        return "Este módulo insertará las rutas de su proyecto en su página personalizada.";
    }

    public function addFieldsContentAdmin(Form $builder, $container, $module) {
        /** @var Helper $baseHelper */
        $baseHelper = $container->get('trazeo_base_helper');
        $cities = $baseHelper->getAllCitiesUsed();

        $builder->add('contentArray', 'choice', array(
            'choices' => $cities,
            'multiple' => true,
            'label' => "¿De qué Ciudad desea mostrar los Grupos?"
        ));

        return $builder;
    }

    function renderFront($container, Module $module = null) {
        $vars = $this->prepareFront($container, $module);
        return $container->renderView('TrazeoMyPageBundle:Module:TrazeoRoutes.html.twig', array('module' => $module, 'vars' => $vars));
    }
}