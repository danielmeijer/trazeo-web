<?php
namespace Trazeo\BaseBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

class TestEmailCommand extends ContainerAwareCommand
{
    // php app/console trazeo:test_email

    protected function configure()
    {
        $this
            ->setName('trazeo:test_email')
            ->setDescription('Generate email for test email and autologin');
    }

    /**
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $con = $this->getContainer();
        $con->enterScope('request');
        $con->set('request', new Request(), 'request');
        $context = $this->getContainer()->get('router')->getContext();
        /** @var EntityManager $em */
        $em  = $con->get('doctrine.orm.default_entity_manager');
        $repositoryUserExtend = $em->getRepository('ApplicationSonataUserBundle:User');
        $user = $repositoryUserExtend->findOneByEmail('aarrabal@sopinet.com');
        $output->writeln('<comment>Poniendo en cola de envío por email</comment>');
        $mailer = $con->get('trazeo_mailer_helper');
        $link=$con->get('urlhelper')->generateUrl('panel', $user);
        $message = $mailer->createNewMessage('info@trazeo.com', 'Trazeo', 'jmartos89@hotmail.com', 'Esto es pa ti Juan', '<h2>Esto es un es para ver</h2><a href="'.$link.'">Link</a>');
        $mailer->sendMessage($message);
        $output->writeln('<info>Hecho</info>');
    }
}
