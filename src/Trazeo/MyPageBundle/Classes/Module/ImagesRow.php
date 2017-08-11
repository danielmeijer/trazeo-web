<?php

namespace Trazeo\MyPageBundle\Classes\Module;

use Symfony\Component\Form\Form;
use Trazeo\MyPageBundle\Classes\ModuleAbstract;
use Trazeo\MyPageBundle\Entity\Module;

class ImagesRow extends ModuleAbstract {
    public function getStyle(Module $module) {
        return "padding-left: 2.5%";
    }

    public function addFieldsContentAdmin(Form $builder, $container, $module) {
        $builder->add('content', 'text', array(
            'label' => "URL de las imagenes separadas por |"
        ));
        return $builder;
    }

    public function getAdminDescription(Module $module) {
        return "Este módulo insertará varias imagenes, en una fila, en su página personalizada. Indique las URL de las mismas.";
    }
}