<?php
namespace Trazeo\MyPageBundle\Classes\Module;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Form;
use Trazeo\BaseBundle\Service\Helper;
use Trazeo\MyPageBundle\Classes\ModuleAbstract;
use Trazeo\MyPageBundle\Entity\Module;

class TrazeoRoutes extends ModuleAbstract {
    function prepareFront($container, Module $module = null) {
        $groups_custom = $module->getMenu()->getPage()->getGroups();
        return $groups_custom->toArray();
    }

    public function getAdminDescription(Module $module) {
        return "Este módulo insertará las rutas de su proyecto en su página personalizada.";
    }

    public function addFieldsContentAdmin(Form $builder, $container, $module) {
        // No añadimos ningún parámetro de configuración
        return $builder;
    }
}