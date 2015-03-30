<?php
namespace Sopinet\Bundle\ChatBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Sopinet\GCMBundle\Entity\Device;
use Sopinet\GCMBundle\Model\Msg;

class MessageRepository extends EntityRepository{
    /**
     * Guarda un msg en la base de datos
     *
     * @param Msg $msg
     *
     * @return Message
     */
    public function addMsg(Msg $msg)
    {
        $em = $this->getEntityManager();
        $message=$this->msgToMessage($msg);
        $em->persist($message);
        $em->flush();

        return $message;
    }

    /**
     * Guarda un mensaje en la base de datos
     *
     * @param Message $msg
     *
     * @return Message
     */
    public function addMessage(Message $message) {
        $em = $this->getEntityManager();
        $em->persist($message);
        $em->flush();

        return $message;
    }

    public function messageToMsg(Message $message){
        $msg=new Msg();
        $msg->type=$message->getType();
        $msg->time=$message->getDateSend();
        $msg->chatid=$message->getChat()->getId();
        $msg->from=$message->getDevice()->getToken();
        $msg->chattype=$message->getChat()->getType();
        $msg->msgid=$message->getId();
        $msg->phone=$message->getUser()->getMobile();#FIXME
        $msg->text=$message->getText();
        $msg->device=$message->getDevice()->getType();
        return $msg;
    }

    public function msgToMessage(Msg $msg){
        /** @var EntityManager $em */
        $em = $this->getEntityManager();
        $message = new Message();
        $message->setDateSend(new \DateTime((int) $msg->time/1000));
        $message->setDateReceieved(new \DateTime((int) $msg->time/1000));
        $chat=$em->getRepository('SopinetChatBundle:Chat')->find($msg->chatid);
        if($chat==null)throw new EntityNotFoundException();
        $message->setChat($chat);
        /** @var Device $device */
        $device=$em->getRepository('SopinetGCMBundle:Device')->findOneByToken($msg->from);
        if($device==null)throw new EntityNotFoundException();
        $message->setDevice($device);
        $message->setUser($device->getUser());
        $message->setText($msg->text);
        return $message;
    }
}