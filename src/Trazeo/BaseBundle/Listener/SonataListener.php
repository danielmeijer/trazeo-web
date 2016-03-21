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
            'pre_update'
        );
    }

    /**
     *  Método que ejecuta las acciones del listener en el post persist
     *
     * @param PersistenceEvent $event
     */
    public function onPreUpdate(PersistenceEvent $event)
    {
        $chatRepository=$this->_em->getRepository('SopinetChatBundle:Chat');
        $group=$event->getObject();
        $chat=$chatRepository->findOneByGroup($event->getObject());

    }
}