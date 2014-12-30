<?php

namespace Trazeo\MyPageBundle\Classes\Module;

use Trazeo\MyPageBundle\Classes\ModuleAbstract;
use Trazeo\MyPageBundle\Entity\Module;

class ComboBigIntro extends ModuleAbstract {
    public function getStyle(Module $module) {
        return "background-image: url('".$this->getContentByPart($module)."')";
    }
}