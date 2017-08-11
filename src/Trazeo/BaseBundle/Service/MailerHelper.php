<?php

namespace Trazeo\BaseBundle\Service;

use Hip\MandrillBundle\Message;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Swift_Message;
class MailerHelper {
	private $_container;

    function __construct(ContainerInterface $container) {
        $this->_container = $container;
    }

    public function createNewMessage($from='hola@trazeo.es', $fromName='Trazeo', $email, $subject, $body){
        $mailer=$this->_container->getParameter('mailer_service');
        if ($mailer=='swiftMailer') {
            $message = new Swift_Message();
            $message
                ->setFrom($from, $fromName)
                ->addTo($email)
                ->setSubject($subject)
                ->setBody($body,'text/html');

            return $message;

        } else {
            $message = new Message();
            $message
                ->setFromEmail($from)
                ->setFromName($fromName)
                ->addTo($email)
                ->setSubject($subject)
                ->setHtml($body);

            return $message;
        }
    }

    public function sendMessage($message) {
        $mailer=$this->_container->getParameter('mailer_service');
        if ($mailer=='swiftMailer') {
            /** @var \Swift_Mailer $dispatcher */

            $dispatcher = $this->_container->get('swiftmailer.mailer');
            $dispatcher->send($message);
        } else {
            $dispatcher = $this->_container->get('hip_mandrill.dispatcher');
            $dispatcher->send($message);
        }

    }
}
