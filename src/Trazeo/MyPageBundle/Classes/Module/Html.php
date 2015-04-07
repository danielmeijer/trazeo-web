<?php

namespace Trazeo\MyPageBundle\Classes\Module;

use Symfony\Component\Form\Form;
use Trazeo\MyPageBundle\Classes\ModuleAbstract;
use Trazeo\MyPageBundle\Entity\Module;

class Html extends ModuleAbstract {
    public function getAdminDescription(Module $module) {
        return "Este módulo insertará código HTML en su página personalizada.";
    }

    public function addFieldsContentAdmin(Form $builder, $container, $module) {
        $builder->add('content', 'ckeditor', array(
            'label' => 'HTML que contiene el módulo'
        ));
        return $builder;
    }
}