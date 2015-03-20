<?php
namespace Trazeo\BaseBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Application\Sonata\UserBundle\Entity\User;
use Trazeo\BaseBundle\Entity\EChildRide;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RequestContext;
use Sopinet\Bundle\SimplePointBundle\ORM\Type\SimplePoint;
use Trazeo\BaseBundle\Entity\ERide;

class TempUpgradeRidesCommand extends ContainerAwareCommand
{
    # php app/console trazeo:distances

    protected function configure()
    {
        $this
            ->setName('trazeo:tempUpgradeRides')
            ->setDescription('Actualizar temporalmente los datos de Rides');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $con = $this->getContainer();
        $con->enterScope('request');
        $con->set('request', new Request(), 'request');
        $em = $con->get('doctrine')->getManager();

        //Sacar paseos cuya distancia sea nula
        $rides = $em->getRepository("TrazeoBaseBundle:ERide")->findAll();

        $repositoryGroup = $em->getRepository("TrazeoBaseBundle:EGroup");

        $output->writeln("Comienza la actualización de paseos");

        /** @var ERide $ride */
        foreach($rides as $ride) {
            if ($ride->getGroup() == null && $ride->getGroupRegistered() != null) {
                $ride->setGroupid($ride->getGroupRegistered()->getId());
                $em->persist($ride);
                $em->flush();
                $output->writeln("Actualizado paseo con ID: ".$ride->getId());
            }
            /**
             * Antiguo actualizador
            if ($ride->getGroupid() != null && $ride->getGroupid() != 0) {
                $group = $repositoryGroup->findOneById($ride->getGroupid());
                $ride->setGroupRegistered($group);
                $em->persist($ride);
                $em->flush();
                $output->writeln("Actualizado paseo con ID: ".$ride->getId());
            }
             * */
        }
    }
}