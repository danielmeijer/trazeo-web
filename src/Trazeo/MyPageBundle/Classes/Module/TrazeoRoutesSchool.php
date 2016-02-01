<?php
namespace Trazeo\MyPageBundle\Classes\Module;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use Trazeo\MyPageBundle\Classes\ModuleAbstract;
use Trazeo\MyPageBundle\Entity\Module;

class TrazeoRoutesSchool extends ModuleAbstract {
    function prepareFront($container, Module $module = null) {
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();
        $repositoryGroup = $em->getRepository('TrazeoBaseBundle:EGroup');

        $groups_from_school = $repositoryGroup->findBy(array('school1' => $module->getContent()));

        $array1 = array();
        $array1 = $groups_from_school;

        return $array1;
    }

    public function getAdminDescription(Module $module) {
        return "Este módulo insertará las rutas de un colegio en su página personalizada.";
    }

    public function addFieldsContentAdmin(Form $builder, $container, $module) {
        // No añadimos ningún parámetro de configuración
        return $builder;
    }
}