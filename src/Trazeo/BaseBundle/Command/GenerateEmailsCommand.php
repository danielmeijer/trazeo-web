<?php
namespace Trazeo\BaseBundle\Command;

use Sopinet\Bundle\UserNotificationsBundle\Entity\Notification;
use Sopinet\GCMBundle\Entity\Device;
use Sopinet\GCMBundle\Model\Msg;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Application\Sonata\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Swift_Message as Message;
use Hip\MandrillBundle\Dispatcher;

class GenerateEmailsCommand extends ContainerAwareCommand
{
    // php app/console trazeo:emails now
    // php app/console trazeo:emails important
    // php app/console swiftmailer:spool:send

    protected function configure()
    {
        $this
            ->setName('trazeo:emails')
            ->setDescription('Generate emails from notifications alerts')
            ->addArgument(
                'time',
                InputArgument::REQUIRED,
                'Tiempo de configuración para el envío de correos'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
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

        foreach ($users as $user) {
            $reNOT = $em->getRepository("SopinetUserNotificationsBundle:Notification");

            //Important is now defined as ride.finish and group.invite.user
            if ($time=='important') {
                $notifications = $reNOT->findBy(array('user' => $user, 'email' => 0,'action' => 'ride.finish'));
                $notifications2= $reNOT->findBy(array('user' => $user, 'email' => 0,'action' => 'group.invite.user'));
                $notifications3= $reNOT->findBy(array('user' => $user, 'email' => 0,'action' => 'timeline.newFromMonitor'));
                $notifications=array_merge($notifications, $notifications2, $notifications3);
            } else {
                $notifications = $reNOT->findBy(array('user' => $user, 'email' => 0));
            }

            if (count($notifications) > 0) {
                $output->writeln('<comment>Poniendo en cola de envío por email '.count($notifications).' notificaciones para el usuario '.$user->getUser()->getEmail().'</comment>');

                /** @var Notification $not */
                foreach ($notifications as $not) {
                    $not->setEmail(1);
                    $em->persist($not);
                    $em->flush();
                }

                if (count($notifications) == 1) {
                    $not  = $con->get('sopinet_user_notification');
                    $stringNot = $not->parseNotification($notifications[0], "title");
                    $subject=$stringNot;
                } else {
                    $subject=("Tiene ".count($notifications)." novedades");
                }

                $mailer = $con->get('trazeo_mailer_helper');
                $message = $mailer->createNewMessage('no-reply@trazeo.es', 'Trazeo', $user->getUser()->getEmail(), $subject, $con->get('templating')->render('SopinetTemplateSbadmin2Bundle:Emails:notifyUser.html.twig', array('user' => $user, 'notifications' => $notifications)));
                $mailer->sendMessage($message);
                $output->writeln('<info>Hecho</info>');
            }
        }
    }
}
