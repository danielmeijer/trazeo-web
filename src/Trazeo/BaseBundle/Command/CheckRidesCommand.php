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
    	//Sacar grupos en marcha
    	$rides = $em->getRepository("TrazeoBaseBundle:ERide")->findByGroupid(null);

    	foreach($rides as $ride){
    		
    		
    		/*
    		//Ordenar los eventos de cada grupo
    		$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');
    		 
    		$query = $reEvent->createQueryBuilder('e')
    		->where('e.ride = :ride')
    		->setParameters(array('ride' => $ride))
    		->orderBy('e.createdAt', 'DESC')
    		->getQuery();
    		 
    		$events = $query->getResult();

	    	$lastEvent = $events[0];
	    	$lastEventDate = $lastEvent->getCreatedAt();
	    	$lastEventTimestamp = $lastEventDate->getTimestamp();
	
	    	$output->writeln('<info>Revisando si el último evento creado fue hace más de 5min....</info>');
	    	$output->writeln('<info>Eliminando...</info>');
	    	*/
	    	// Current datetime
	    	$dateCurrent = new \DateTime();
	
	    	$now = $dateCurrent->getTimestamp();
	    	
	    	$rideupdated = $ride->getUpdatedAt()->getTimestamp();

	    	// Nº de minutos en formato timestamp(5min = 300)
	    	$minutes = $now - $rideupdated;

	 		$rideGroup = $ride->getGroup();
	 		
	 		if($minutes >= 900 && $rideGroup != null){
	 		
	 			// Detener el paseo del grupo
	 			$rideGroup->setHasRide(0);
	 			
	 			$em->persist($rideGroup);
	 			$em->flush();
	 			
	 			//Cálculo del tiempo transcurrido en el paseo
	 			$inicio = $ride->getCreatedAt();
	 			$fin = new \DateTime();
	 			
	 			$diff = $inicio->diff($fin);
	 			$duration = $diff->h." horas, ".$diff->i." minutos y ".$diff->s." segundos";
	 			
	 			$ride->setDuration($duration);
	 			$ride->setGroupid($rideGroup->getId());
	 			$ride->setGroup(null);
	 			$em->persist($ride);
	 			$em->flush();
	 			
	 			//desvinculamos a los niños del paseo
	 			$childs = $em->getRepository('TrazeoBaseBundle:EChild')->findByRide($ride);

	 			foreach ($childs as $child){
	 				$child->setRide(null);
	 				$em->persist($child);
	 			}
	 			$em->flush();
	 				
	 			$event = new EEvent();
	 			$event->setRide($ride);
	 			$event->setAction("finish");
	 			$event->setData("");
	 			//$event->setLocation(new SimplePoint($latitude, $longitude));
	 			$em->persist($event); 			
	 						
	 			$em->flush();
	 			
	 			$output->writeln('<fg=yellow>Paseo del grupo ' . $rideGroup->getName() . ' detenido</fg=yellow>');
	 		}else{
	 			
	 			$output->writeln('<fg=yellow>No hay ningún paseo que detener</fg=yellow>');
	 		}
    	}
    	
    	$output->writeln('<info>Hecho</info>');
    }
}
    