<?php
namespace Sopinet\GCMBundle\Command;

use Sopinet\GCMBundle\Event\GCMEvent;
use Sopinet\GCMBundle\GCMEvents;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sopinet\GCMBundle\Model\Msg;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class xmppServerListenerCommand extends ContainerAwareCommand
{
# php app/console gcmbundle:server
    protected function configure()
    {
        $this
            ->setName('gcmbundle:server')
            ->setDescription('Start server listener XMPP');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $container;
        $container = $this->getContainer();

        $senderID = $container->getParameter("gcmbundle_senderid");
        $key = $container->getParameter("gcmbundle_key");


        $output->writeln('<info>SENDERID: '.$senderID.'</info>');
        $output->writeln('<info>KEY: '.$key.'</info>');

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


        $client->getEventManager('on_normal_message', function($message) {
           ldd($message);
        });

        // Conectamos a GCM
        $client->connect();
 *
 *
**/
        // TODO: NO ESTAMOS MONTANDO EL SERVIDOR XMPP

        /**
        // TODO: Falta el USE
        $client = new JAXL(array(
            'jid' => $senderID.'@gcm.googleapis.com',
            'pass' => $key,
            'host' => 'gcm.googleapis.com',
            'port' => 5235,
            'force_tls' => true,
            'auth_type' => 'PLAIN',
            'strict' => FALSE,
            'ssl' => TRUE,
            'log_level' => JAXL_DEBUG // TODO: Delete log_level o configurar
        ));

        $client->enable_unix_sock();

        $client->add_cb('on_normal_message', function($message) {
            global $container;

            // Obtenemos el texto con toda la información
            $text = str_replace("&quot;", '"', $message->childrens[0]->text);
            $text_json = json_decode($text);

            // Construímos el MSG
            $msg = new Msg();
            $msg->text = $text_json->data->text;
            $msg->type = $text_json->data->type;
            $msg->from = $text_json->from;
            $msg->chatid = $text_json->data->chatid;

            // Lanzamos el evento
            //if ($msg->from != "NO") {
                $msgEvent = new GCMEvent($container, $msg);
                $container->get('event_dispatcher')->dispatch(GCMEvents::RECEIVED, $msgEvent);
            //}
        });

        // TODO: DEBUG! $client->start();
        $client->start(array(
            '--with-unix-sock' => true
        ));
         **/
    }
}