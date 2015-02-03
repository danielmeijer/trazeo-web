<?php
namespace Trazeo\BaseBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Application\Sonata\UserBundle\Entity\User;
use Trazeo\BaseBundle\Entity\EEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Sopinet\Bundle\SimplePointBundle\ORM\Type\SimplePoint;
use Trazeo\BaseBundle\Entity\ERide;
use Trazeo\BaseBundle\Entity\ERideRepository;

class UpdateChildrenInRidesCommand extends ContainerAwareCommand
{
    # php app/console trazeo:rides

    protected function configure()
    {
        $this
            ->setName('trazeo:childrenInRides')
            ->setDescription('Actualiza la información de niños en los paseos ya realizados');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $con = $this->getContainer();
        //$con->enterScope('request');
        //$con->set('request', new Request(), 'request');

        $em = $con->get('doctrine')->getManager();

        //Sacar grupos en marcha
        /** @var ERideRepository $repositoryERide */
        $repositoryERide = $em->getRepository("TrazeoBaseBundle:ERide");
        $rides = $repositoryERide->findAll();
        //$rides = $repositoryERide->findByChildsDone("false");

        /** @var ERide $ride */
        foreach ($rides as $ride) {
            $repositoryERide->getChildrenInRide($ride);
            $output->writeln('<fg=yellow>Paseo con ID ' . $ride->getId() . ' actualizado</fg=yellow>');
        }
    }
}