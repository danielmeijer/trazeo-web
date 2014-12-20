<?php

namespace Trazeo\MyPageBundle\Classes\Module;

use Trazeo\MyPageBundle\Classes\ModuleAbstract;
use Trazeo\MyPageBundle\Entity\Module;

class ComboBigIntro extends ModuleAbstract {
    public function getStyle(Module $module) {
        die($module->getContent());
        return "background-image: url('http://segurosbaratos.motorgiga.com/uploads/comparador_seguros_de_coche.jpg')";
    }
}