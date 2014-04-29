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
    		$notifications = $reNOT->findBy(array('user' => $user, 'email' => 0));
    		if (count($notifications) > 0) {
    			$output->writeln('<comment>Poniendo en cola de envío por email '.count($notifications).' notificaciones para el usuario '.$user->getUser()->getEmail().'</comment>');
    			    			
    			$message = \Swift_Message::newInstance()
    			// TODO: Traducir
    			->setSubject("Tiene ".count($notifications)." novedades")
    			->setFrom(array("info@trazeo.com" => "Trazeo"))
    			->setTo($user->getUser()->getEmail())
    			//->setCc($setCC)
    			->setBody($con->get('templating')->render('SopinetTemplateSbadmin2Bundle:Emails:notifyUser.html.twig', array('user' => $user, 'notifications' => $notifications)), 'text/html');
    			//->setBody("Este es el cuerpo del delito");
    			$ok = $con->get('mailer')->send($message);
    			ld($ok);    			

    			$output->writeln('<info>Hecho</info>');
    		}
    	}
    }
}
