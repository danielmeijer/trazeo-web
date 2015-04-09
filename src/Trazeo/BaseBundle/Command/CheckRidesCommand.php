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

        /** @var ERide $ride */
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
                $ride->setGroupRegistered($rideGroup);
	 			$ride->setGroup(null);
	 			$em->persist($ride);
	 			$em->flush();

                $userextends = $rideGroup->getUserextendgroups();

                $not = $con->get('sopinet_user_notification');
                $repositoryUserExtend = $em->getRepository('TrazeoBaseBundle:UserExtend');

                foreach($userextends as $userextend)
                {
                    if ($repositoryUserExtend->hasChildOnRide($userextend,$ride)) {
                        $url=$con->get('trazeo_base_helper')->getAutoLoginUrl($userextend->getUser(),'panel_ride_resume', array('id' => $ride->getId()));
                        $not->addNotification(
                            "ride.finish",
                            "TrazeoBaseBundle:EGroup",
                            $rideGroup->getId(),
                            $url,
                            $userextend->getUser(),
                            null,
                            $con->get('router')->generate('panel_ride_current', array('id' => $ride->getId()))
                        );
                        $repositoryDevice=$em->getRepository('SopinetGCMBundle:Device');
                        $devices=$repositoryDevice->findByUser($userextend);
                        $gcmHelper=$con->get('sopinet_gcmhelper');
                        /** @var Device $device */
                        foreach ($devices as $device) {
                            $time=new \DateTime('now');
                            $gcmHelper->sendNotification('showMessage', $rideGroup->getId(), "ride.finish", $time, $userextend->getUser()->getPhone(), $device->getToken(), $device->getType());
                        }
                        //Si el usuario no tiene ningun niño en el paseo se manda la notificación pero no se muestra
                    } else {
                        $repositoryDevice=$em->getRepository('SopinetGCMBundle:Device');
                        $devices=$repositoryDevice->findByUser($userextend);
                        $gcmHelper=$con->get('sopinet_gcmhelper');
                        /** @var Device $device */
                        foreach ($devices as $device) {
                            $time=new \DateTime('now');
                            $gcmHelper->sendNotification('', $rideGroup->getId(), "ride.finish", $time, $userextend->getUser()->getPhone(), $device->getToken(), $device->getType());
                        }
                    }
                }

	 			//desvinculamos a los niños del paseo
	 			$childs = $em->getRepository('TrazeoBaseBundle:EChild')->findByRide($ride);

	 			foreach ($childs as $child){
	 				$child->setRide(null);
	 				$child->setSelected(0);
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
    