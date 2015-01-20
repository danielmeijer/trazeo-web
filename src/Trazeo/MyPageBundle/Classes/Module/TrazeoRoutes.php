<?php
namespace Trazeo\MyPageBundle\Classes\Module;

use Doctrine\ORM\EntityManager;
use Trazeo\MyPageBundle\Classes\ModuleAbstract;
use Trazeo\MyPageBundle\Entity\Module;

class TrazeoRoutes extends ModuleAbstract {
    function prepareFront($container, Module $module = null) {
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();
        $repositoryGroup = $em->getRepository('TrazeoBaseBundle:EGroup');

        $groups_from_ciy = $repositoryGroup->findBy(array('city' => $module->getContent()));
        $groups_custom = $module->getMenu()->getPage()->getGroups();

        $array1 = array();
        if ($groups_from_ciy != null) $array1 = $groups_from_ciy->toArray();

        $array2 = array();
        if ($groups_custom != null) $array2 = $groups_custom->toArray();

        return array_merge($array1, $array2);
    }
}