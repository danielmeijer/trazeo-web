<?php
namespace Sopinet\GCMBundle\Service;
use RMS\PushNotificationsBundle\Exception\InvalidMessageTypeException;
use RMS\PushNotificationsBundle\Message\AndroidMessage;
use RMS\PushNotificationsBundle\Message\iOSMessage;
use Sopinet\GCMBundle\Model\Msg;
use Symfony\Component\DependencyInjection\ContainerInterface;

class GCMHelper
{
    private $_container;
    function __construct(ContainerInterface $container)
    {
        $this->_container = $container;
    }

    /**
     * Añade un dispositivo a un usuario en la base de datos
     *
     * @param String $deviceId
     * @param $user
     * @param String $type('Android'|'iOS')
     *
     * @return mixed
     */
    public function addDevice($deviceId, $user, $token, $type='Android')
    {
        $em = $this->_container->get("doctrine.orm.entity_manager");
        $reDevice = $em->getRepository('SopinetGCMBundle:Device');

        return $reDevice->addDevice($deviceId, $user, $token, $type);
    }

    /*private function sendGoogleCloudMessage( $apiKey, $data, $id )
    {
        //------------------------------
        // Replace with real GCM API
        // key from Google APIs Console
        //
        // https://code.google.com/apis/console/
        //------------------------------

        //------------------------------
        // Define URL to GCM endpoint
        //------------------------------

        $url = 'https://android.googleapis.com/gcm/send';

        //------------------------------
        // Set GCM post variables
        // (Device IDs and push payload)
        //------------------------------

        $post = array(
            'to'		=> $id,
            //'registration_ids'  => $ids,
            'data'              => $data,
        );

        //------------------------------
        // Set CURL request headers
        // (Authentication and type)
        //------------------------------

        $headers = array(
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );

        //------------------------------
        // Initialize curl handle
        //------------------------------

        $ch = curl_init();

        //------------------------------
        // Set URL to GCM endpoint
        //------------------------------

        curl_setopt( $ch, CURLOPT_URL, $url );

        //------------------------------
        // Set request method to POST
        //------------------------------

        curl_setopt( $ch, CURLOPT_POST, true );

        //------------------------------
        // Set our custom headers
        //------------------------------

        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );

        //------------------------------
        // Get the response back as
        // string instead of printing it
        //------------------------------

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        //------------------------------
        // Set post data as JSON
        //------------------------------

        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $post ) );

        //------------------------------
        // Actually send the push!
        //------------------------------

        $result = curl_exec( $ch );

        //------------------------------
        // Error? Display it!
        //------------------------------

        if ( curl_errno( $ch ) )
        {
            echo 'GCM error: ' . curl_error( $ch );
        }

        //------------------------------
        // Close curl handle
        //------------------------------

        curl_close( $ch );

        //------------------------------
        // Debug GCM response
        //------------------------------

        // TODO: Esto se podría guardar en un LOG,
        // echo $result;
    }*/


    /*       public function sendMessage(Msg $msg, $to) {
               // Variables de conexión a GCM
               $senderID = $this->_container->getParameter('gcmbundle_senderid');

               $message_internal = json_encode($msg);
               //$message_external = json_decode($message_internal);
               $mes['type'] = $msg->type;
               $mes['text'] = $msg->text;
               $mes['chatid'] = $msg->chatid;
               $mes['chattype'] = $msg->chattype;
               $mes['msgid'] = $msg->msgid;
               $mes['phone'] = $msg->phone;
               $mes['time'] = $msg->time;
               //$mes['from'] = "NO";
               // echo "\n\rMandamos mensaje\n\r";
               if($msg->device==$msg::ANDROID){
                   $this->sendGCMessage($mes, $to);
               }
               elseif($msg->device==$msg::IOS){
                   $this->sendAPNMessage($mes,$to);
               }
               /**
               // Iniciamos LOG
               $logger = new Logger('xmpp');

               $logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

               // Parámetros de conexión a GCM
               $hostname       = 'gcm.googleapis.com';
               $port           = 5235;
               $connectionType = 'tls';
               $address        = "$connectionType://$hostname:$port";

               $username = $senderID.'@gcm.googleapis.com';
               $password = $key;

               $options = new Options($address);
               $options->setLogger($logger)
                   ->setUsername($username)
                   ->setPassword($password);

               // Creamos el cliente de conexión
               $client = new Client($options);

               // Creamos el mensaje
               $message = new Message();
               $message->setTo($to);

               $message_internal = json_encode($msg);

               $message->setMessage($message_internal);
               $message->setType("normal");

               // Conectamos a GCM
               $client->connect();

               // Enviamos el mensaje a GCM
               $client->send($message);

               // Desconectamos
               $client->disconnect();
                * */
    //}

    /**
     * @param Msg $msg
     * @param String $to
     *
     */
    public function sendMessage(Msg $msg, $to)
    {
        $mes['type'] = $msg->type;
        $mes['text'] = $msg->text;
        $mes['chatid'] = $msg->chatid;
        $mes['chattype'] = $msg->chattype;
        $mes['msgid'] = $msg->msgid;
        $mes['phone'] = $msg->phone;
        /** @var \DateTime $time */
        $mes['time'] =$msg->time;
        $mes['groupId']= $msg->groupId;
        $mes['username']=$msg->username;
        if ($msg->device==$msg::ANDROID) {
            $this->sendGCMessage($mes, $to);
        } elseif ($msg->device==$msg::IOS) {
            $this->sendAPNMessage($mes, $to);
        }
    }

    /**
     * Funcion que envia un mensaje con el servicio GCM de Google
     * @param $mes
     * @param $to
     */
    private function sendGCMessage($mes, $to)
    {
        $message=new AndroidMessage();
        $message->setMessage($mes['text']);
        $message->setData($mes);
        $message->setDeviceIdentifier($to);
        $message->setGCM(true);
        try {
            $this->_container->get('rms_push_notifications')->send($message);
        } catch (InvalidMessageTypeException $e) {
            throw $e;
        }
    }


    /**
     * Funcion que envia un mensaje con el sevricio APN de Apple
     * @param Msg $mes
     * @param String $to
     *
     * @throws \InvalidArgumentException
     */
    private function sendAPNMessage($mes, $to)
    {
        $message=new iOSMessage();
        try {
            $message->setData($mes);
        } catch (\InvalidArgumentException $e) {
            throw $e;
        }
        $message->setMessage($mes['text']);
        $message->setDeviceIdentifier($to);
        $this->_container->get('rms_push_notifications')->send($message);
    }

    /**
     * @param $text
     * @param $from
     * @param $type
     * @param $time
     * @param $phone
     * @param $toToken
     */
    public function sendNotification($text, $groupId, $type, $time, $phone, $toToken, $deviceType)
    {
        $mes['type'] = $type;
        $mes['text'] = $text;
        $mes['groupId']= $groupId;
        $mes['phone'] = $phone;
        $mes['time'] =$time->getTimestamp();
        if ($deviceType==Msg::ANDROID) {
            $this->sendGCMessage($mes, $toToken);
        } elseif ($deviceType==Msg::IOS) {
            $this->sendAPNMessage($mes, $toToken);
        }
    }
}
?>