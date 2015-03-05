<?php
    // TODO Recepción del evento

// src/Acme/SearchBundle/EventListener/SearchIndexerSubscriber.php
namespace Sopinet\Bundle\ChatBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use PetyCash\AppBundle\Entity\MessageRepository;
use Sopinet\GCMBundle\Event\GCMEvent;
use Sopinet\GCMBundle\GCMEvents;
use Sopinet\GCMBundle\Model\Msg;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;
use Sopinet\GCMBundle\Entity\Device;
use Application\Sopinet\UserBundle\Entity\User;

class MsgListener implements EventSubscriber
{
    protected $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }
    public function getSubscribedEvents()
    {
        return array(
            GCMEvents::RECEIVED => 'onGCMReceived',
        );
    }

    /**
     * Recibe un mensaje de GCM y lo tramita
     *
     * @param GCMEvent $event
     */
    public function onGCMReceived(GCMEvent $event)
    {
        $em = $this->em;
        $msg = $event->getMsg();

        // Obtenemos los devices del chatid
        $repositoryChat = $em->getRepository("SopinetChatBundle:Chat");
        $devices = $repositoryChat->getDevices($msg->chatid);

        // Si no hay dispositivos que notificar: salimos
        if (!is_array($devices)) return false;

        // Comprobamos permisos (el from tiene permiso para trabajar en el chatid)
        $ok = false;
        foreach($devices as $device) {
            /* @var $device Device */
            if ($device->getToken() == $msg->from) {
                $ok = true;
                // Obtenemos el número de teléfono y el usuario
                $msg->phone = $device->getUser()->getPhone();
            }
        }
        if (!$ok) return false; // Ha sucedido algo inesperado, ha mandado un mensaje alguien que no estaba en el Chat.
        // TODO: Podríamos lanzar aquí la excepción apropiada.

        // Enviamos el mensaje correspondiente a todos los dispositivos, excepto
        // al que está enviando este mensaje.
        foreach ($devices as $device) {
            if ($device->getToken() != $msg->from) {
                /* @var $container Container */
                $container = $event->getContainer();
                $gcmhelper = $container->get('sopinet_gcmhelper');
                $msg->device=$device->getType();
                $gcmhelper->sendMessage($msg, $device->getToken());
            }
        }

        /** @var MessageRepository $repositoryMessage */
        $repositoryMessage = $em->getRepository("SopinetChatBundle:Message");
        $repositoryMessage->addMsg($msg);
    }
}