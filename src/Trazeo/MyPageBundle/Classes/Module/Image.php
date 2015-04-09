<?php

namespace Trazeo\MyPageBundle\Classes\Module;

use Symfony\Component\Form\Form;
use Trazeo\MyPageBundle\Classes\ModuleAbstract;
use Trazeo\MyPageBundle\Entity\Module;

class Image extends ModuleAbstract {
    public function addFieldsContentAdmin(Form $builder, $container, $module) {
        $builder->add('content', 'text', array(
            'label' => "URL de la imagen"
        ));
        return $builder;
    }

    public function getAdminDescription(Module $module) {
        return "Este módulo insertará una imagen en su página personalizada. Indique la URL de la misma.";
    }
}