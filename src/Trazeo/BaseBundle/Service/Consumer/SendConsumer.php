<?php

namespace Trazeo\BaseBundle\Service\Consumer;
use Doctrine\ORM\EntityManager;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use RMS\PushNotificationsBundle\Exception\InvalidMessageTypeException;
use RMS\PushNotificationsBundle\Message\AndroidMessage;
use Symfony\Component\DependencyInjection\Dump\Container;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class SendConsumer
 * @package Trazeo\BaseBundle\Service\Consumer;
 */
class SendConsumer implements ConsumerInterface
{
    protected $request;

    public function setRequest(RequestStack $request_stack)
    {
        $this->request = $request_stack->getCurrentRequest();
    }

    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Process the message
     *
     * @param AMQPMessage $msg
     */
    public function execute(AMQPMessage $msg)
    {
        $jsonBody = json_decode($msg->body);
        $mes = array();
        $mes['type'] = $jsonBody->type;
        $mes['text'] = $jsonBody->text;
        $mes['chatid'] = $jsonBody->chatid;
        $mes['chattype'] = $jsonBody->chattype;
        $mes['msgid'] = $jsonBody->msgid;
        $mes['phone'] = $jsonBody->phone;
        $mes['time'] =$jsonBody->time;
        $mes['groupId']= $jsonBody->groupId;
        $mes['username']=$jsonBody->username;
        $to = $jsonBody->to;

        $message=new AndroidMessage();
        $message->setMessage($mes['text']);
        $message->setData($mes);
        $message->setDeviceIdentifier($to);
        $message->setGCM(true);
        try {
            $this->container->get('rms_push_notifications')->send($message);
        } catch (InvalidMessageTypeException $e) {
            throw $e;
        }
    }
}