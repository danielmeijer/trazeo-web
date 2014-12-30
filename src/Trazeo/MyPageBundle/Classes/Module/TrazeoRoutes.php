<?php
namespace Trazeo\MyPageBundle\Classes\Module;

use Doctrine\ORM\EntityManager;
use Trazeo\MyPageBundle\Classes\ModuleAbstract;
use Trazeo\MyPageBundle\Entity\Module;

class TrazeoRoutes extends ModuleAbstract {
    function prepareFront($container, Module $module = null) {
        /** @var EntityManager $em */
        $em = $container->get('doctrine')->getManager();
        $repositoryRoute = $em->getRepository('TrazeoBaseBundle:ERoute');
        //$repositoryGroup = $em->getRepository('TrazeoBaseBundle:EGroup');
        $routes = $repositoryRoute->findBy(array('city' => $module->getContent()));
        return $routes;
    }
}