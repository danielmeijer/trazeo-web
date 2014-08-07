<?php 
namespace Sopinet\Bundle\UserNotificationsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Application\Sonata\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;

class GenerateEmailsCommand extends ContainerAwareCommand
{
	# php app/console usernotifications:emails
	
    protected function configure()
    {
        $this
            ->setName('usernotifications:emails')
            ->setDescription('Generate emails from notifications alerts')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$con = $this->getContainer();
    	$con->enterScope('request');
    	$con->set('request', new Request(), 'request');
    	
    	$em  = $con->get('doctrine')->getManager();
    	$reUserExtend = $em->getRepository("SopinetUserBundle:SopinetUserExtend");
        $reUserSettings=$em->getRepository("SopinetUserPreferencesBundle:UserSetting");
        $setting=$reUserSettings->findByName('notification_email');
    	$output->writeln('<info>Iniciando lectura de notificaciones....</info>');
    	// More info: http://symfony.com/doc/current/components/console/introduction.html
    	
    	$users = $reUserExtend->findAll();

        $options=$con->parameters['sopinet_user_notifications.types'];
        $options_type=[];
    	foreach ($options as $option)array_push($options_type, $option['type']);

    	// Usuarios con la configuración requerida
    	//$output->writeln('<info>Encontrados '.count($users).' usuarios a tratar</info>');
    	$reUserValue=$em->getRepository("SopinetUserPreferencesBundle:UserValue");
    	foreach($users as $user) {
            $uservalue=$reUserValue->getValue($user,$setting[0]);
            //search for user types

            $key=array_search($uservalue, $options_type);
            if($key==false)$option=$con->parameters['sopinet_user_notifications.default_email'];
            $option=$options[$key]['actions'];
            $option=explode(',',$option[0]);

            $reNOT = $em->getRepository("SopinetUserNotificationsBundle:Notification");

            $notifications_aux=$reNOT->findBy(array('user' => $user, 'email' => 0));
            $notifications=[];
            foreach($notifications_aux as $not) {
                if(in_array($not->getAction(),$option)){
                    array_push($notifications, $not);
                }
            }

    		if (count($notifications) > 0) {
    			$output->writeln('<comment>Poniendo en cola de envío por email '.count($notifications).' notificaciones para el usuario '.$user->getUser()->getEmail().'</comment>');
    			
    			foreach($notifications as $not) {

    				$not->setEmail(1);
    				$em->persist($not);
    				$em->flush();
    			}

    			$message = \Swift_Message::newInstance()
    			->setFrom(array("hola@trazeo.es" => "Trazeo"))
    			->setTo($user->getUser()->getEmail())
    			->setBody($con->get('templating')->render('SopinetTemplateSbadmin2Bundle:Emails:notifyUser.html.twig', array('user' => $user, 'notifications' => $notifications)), 'text/html');
    			
    			if (count($notifications) == 1) {
    				$not  = $con->get('sopinet_user_notification');
    				$string_not = $not->parseNotification($notifications[0], "title");
    				$message->setSubject($string_not);
    			} else {
    				$message->setSubject("Tiene ".count($notifications)." novedades");
    			}
    			
    			$ok = $con->get('mailer')->send($message);

    			$output->writeln('<info>Hecho</info>');
    		}
    	}
    }
}
