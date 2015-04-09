<?php

namespace Trazeo\MyPageBundle\Classes\Module;

use Symfony\Component\Form\Form;
use Trazeo\MyPageBundle\Classes\ModuleAbstract;
use Trazeo\MyPageBundle\Entity\Module;

class ComboBigIntro extends ModuleAbstract {
    public function getStyle(Module $module) {
        return "background-image: url('".$this->getContentByPart($module)."')";
    }

    public function addFieldsContentAdmin(Form $builder, $container, $module) {
        $builder->add('content', 'integer');
        return $builder;
    }
}