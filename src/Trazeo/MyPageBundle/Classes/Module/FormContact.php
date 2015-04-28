<?php

namespace Trazeo\MyPageBundle\Classes\Module;

use Hip\MandrillBundle\Message;
use Symfony\Component\Form\Form;
use Trazeo\MyPageBundle\Classes\ModuleAbstract;
use Trazeo\MyPageBundle\Entity\Module;
use Trazeo\MyPageBundle\Form\FormContactType;
use Symfony\Component\HttpFoundation\Request;

class FormContact extends ModuleAbstract {
    function prepareFront($container, Module $module = null) {
        $formContact = $container->createForm(new FormContactType($container));

        return $formContact->createView();
    }

    function executeAction($container, Module $module, Request $request) {
        $subdomain = $module->getMenu()->getPage()->getSubdomain();
        $email = $module->getMenu()->getPage()->getDataEmail();

        $flashMessages = $container->get('sopinet_flashMessages');

        //$dispatcher = $container->get('hip_mandrill.dispatcher');
        $dispatcher = $container->get('swiftmailer.mailer');

        $message = new Message();

        $dataForm = $request->get('FormContact');

        $html = "<h2>Email recibido desde formulario de contacto</h2>";
        $html .= "<hr/>";
        $html .= "<div>Nombre: ".$dataForm['name']."</div>";
        $html .= "<div>Email: ".$dataForm['email']."</div>";
        $html .= "<div>Mensaje: ".$dataForm['message']."</div>";

        $message
            ->setFromEmail('hola@trazeo.es')
            ->setFromName('Trazeo')
            ->addTo($email)
            ->setSubject("Trazeo - Formulario de Contacto")
            ->setHtml($html);


        $result = $dispatcher->send($message);

        if (isset($result[0]['status']) && $result[0]['status'] == "sent") {
            $flashMessages->addFlashMessages("success","Mensaje enviado con éxito.");
        } else {
            $flashMessages->addFlashMessages("warning","Ha ocurrido un error.");
        }

        return $container->redirect($container->generateUrl('landingPage', array('subdomain' => $subdomain)));
    }

    public function getAdminDescription(Module $module) {
        return "Este módulo insertará un formulario de contacto en su página personalizada.";
    }

    public function addFieldsContentAdmin(Form $builder, $container, $module) {
        $builder->add('content_0', 'text', array(
            'label' => 'Título que aparecerá antes del Formulario'
        ));
        $builder->add('content_1', 'text', array(
            'label' => 'Descripción que aparecerá debajo del título'
        ));
        return $builder;
    }
}