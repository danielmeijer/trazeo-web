<?php 
namespace Trazeo\BaseBundle\Command;

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
	# php app/console trazeo:emails now
	# php app/console trazeo:emails important
	# php app/console swiftmailer:spool:send
	
    protected function configure()
    {
        $this
            ->setName('trazeo:emails')
            ->setDescription('Generate emails from notifications alerts')
            ->addArgument(
            		'time',
            		InputArgument::REQUIRED,
            		'Tiempo de configuración para el envío de correos'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$time = $input->getArgument('time');
    	$con = $this->getContainer();
    	$con->enterScope('request');
    	$con->set('request', new Request(), 'request');
    	
    	$em  = $con->get('doctrine')->getManager();
    	$reUserValue = $em->getRepository("SopinetUserPreferencesBundle:UserValue");

    	$output->writeln('<info>Iniciando lectura de notificaciones....</info>');
    	// More info: http://symfony.com/doc/current/components/console/introduction.html
    	
    	$users = $reUserValue->getUsersWith("notification_email", $time);
    	
    	// Usuarios con la configuración requerida
    	$output->writeln('<info>Encontrados '.count($users).' usuarios a tratar</info>');
    	
    	foreach($users as $user) {
    		$reNOT = $em->getRepository("SopinetUserNotificationsBundle:Notification");
    		
    		//Important is now defined as ride.finish and group.invite.user
    		if($time=='important'){
    			$notifications = $reNOT->findBy(array('user' => $user, 'email' => 0,'action' => 'ride.finish'));
    			$notifications2= $reNOT->findBy(array('user' => $user, 'email' => 0,'action' => 'group.invite.user'));
    			$notifications=array_merge($notifications,$notifications2);			
    		}
    		
    		else $notifications = $reNOT->findBy(array('user' => $user, 'email' => 0));

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
    			
    			if ($notifications == 1) {
    				$not  = $this->container->get('sopinet_user_notification');
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
