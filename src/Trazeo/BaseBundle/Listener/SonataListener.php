<?php
namespace Trazeo\BaseBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Event\PersistenceEvent;
use Trazeo\BaseBundle\Entity\EGroup;

class SonataListener implements EventSubscriber {
    private $_em;

    /**
     * Constructor de la clase
     *
     * @param EntityManager $em
     */
    function __construct(EntityManager $em) {
        $this->_em = $em;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'post_update'
        );
    }

    /**
     *  Método que ejecuta las acciones del listener en el post persist
     *
     * @param PersistenceEvent $event
     */
    public function onPostUpdate(PersistenceEvent $event)
    {
        $entity=$event->getObject();
        if ($entity instanceof EGroup) {
            $entity = $this->_em->getRepository('TrazeoBaseBundle:EGroup')->find($entity->getId());
            $chat=$entity->getChat();
            $chat->getChatMembers()->clear();
            $users= $entity->getUserextendgroups();
            foreach ($users as $user) {
                $chat->addChatMember($user);
            }
            $this->_em->persist($chat);
            $this->_em->flush($chat);
        }
    }
}