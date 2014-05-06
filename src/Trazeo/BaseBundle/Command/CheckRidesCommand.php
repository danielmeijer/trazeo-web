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

class CheckRidesCommand extends ContainerAwareCommand
{
	# php app/console trazeo:rides
	
    protected function configure()
    {
        $this
            ->setName('trazeo:rides')
            ->setDescription('Generate emails from notifications alerts')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$con = $this->getContainer();
    	$con->enterScope('request');
    	$con->set('request', new Request(), 'request');
    	$em  = $con->get('doctrine')->getManager();
    	
    	$events = $em->getRepository("TrazeoBaseBundle:EEvent")->findBy(array(),array("createdAt" => "DESC"));
    	// Recoger el campo createdAt de cada evento
    	$lastEvent = $events[0];
    	$lastEventDate = $lastEvent->getCreatedAt();

    	$output->writeln('<info>Revisando si el último evento creado fue hace más de una hora....</info>');
    	$output->writeln('<info>Eliminando...</info>');
    	
    	// Current datetime
    	$dateCurrent = new \DateTime();

    	$result = $lastEventDate->diff($dateCurrent);
    	// Nº de horas
 		$hours = $result->h;
 		if($hours > 0){
 			
 			$rideId = $lastEvent->getRide()->getId(); 			
 			$ride = $em->getRepository("TrazeoBaseBundle:ERide")->find($rideId);
 			$rideGroup = $ride->getGroup();
 			
 			// Detener el paseo del grupo
 			$rideGroup->setHasRide(0);
 			$em->persist($rideGroup); 			
 			$em->flush();
 			
 			$output->writeln('<fg=yellow>Paseo del grupo ' . $rideGroup->getName() . ' detenido</fg=yellow>');
 		}else{
 			
 			$output->writeln('<fg=yellow>No hay ningún paseo que detener</fg=yellow>');
 		}
    	
    	$output->writeln('<info>Hecho</info>');
    }
}
    