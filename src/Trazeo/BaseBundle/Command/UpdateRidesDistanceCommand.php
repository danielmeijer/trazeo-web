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

class UpdateRidesDistanceCommand extends ContainerAwareCommand
{
	# php app/console trazeo:distances
	
    protected function configure()
    {
        $this
            ->setName('trazeo:distances')
            ->setDescription('Calculate distances for Rides')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$con = $this->getContainer();
    	$con->enterScope('request');
    	$con->set('request', new Request(), 'request');
    	$em  = $con->get('doctrine')->getManager();
    	//Sacar paseos cuya distancia sea nula 
    	$rides = $em->getRepository("TrazeoBaseBundle:ERide")->findByDistance(null);
    	$measurer = $con->get('trazeo_base_distance_measurer');
        //Obtener usuarios que tengan marcada la opcion de conexion con civiclub
        $reUserValue = $em->getRepository("SopinetUserPreferencesBundle:UserValue");
        $civiclub_setting = $em->getRepository("SopinetUserPreferencesBundle:UserSetting")->findOneByName("civiclub_conexion");
    	//Para cada paseo calculamos la distancía recorrida en total 
    	foreach($rides as $ride){
    		$distance=0;
    		$childs=[];
            $users=[];
            $manager=null;
            if($ride->getUserExtend()!=null)$id= $ride->getUserExtend()->getId();
            //añadimos los puntos al monitor por acompañar el paseo
            $sopinetuserextend=$em->getRepository("SopinetUserBundle:SopinetUserExtend")->findOneByUser($ride->getUserExtend()->getUser());
            $output->writeln('<info>Monitor'. $ride->getUserExtend() .'</info>');
            $sg = $this->getContainer()->get('sopinet_gamification');
            $sg->addUserAction(
                "Manage_Ride",
                "TrazeoBaseBundle:UserExtend",
                $ride->getUserExtend()->getId(),
                $ride->getUserExtend(),
                1,
                false     
            );
    		//obtenemos todos los niños que pueden haber participado en un paseo 
    		if($ride->getGroupid()!=null){
    			$group = $em->getRepository("TrazeoBaseBundle:EGroup")->findOneById($ride->getGroupid());
    			if($group!=null){
                    $childs=$group->getChilds();
                    $users=$group->getUserextendgroups();
                }	
    		}
    		//si el paseo no ha terminado no actualizamos su distancia
    		else $distance=null;
    		
    		//Calculamos la distancía recorrida por cada niño 
    		foreach ($childs as $child){
				$auxdistance=$measurer->getChildDistance($child,$ride);
                $output->writeln('<info>Niño detectado'. $child . ' distancía '.$auxdistance.'</info>');
				//Si ha recorrido alguna distancía se guarda
				if($auxdistance!=0){
					$childride =new EChildRide();
					$childride->setRide($ride);
					$childride->setChild($child);
					$childride->setDistance($auxdistance);	
					$em->persist($childride);
					$em->flush();       
				}
				$distance+=$auxdistance;
    		}
            //finalmente actualizamos la distancia recorrida en el paseo
            $ride->setDistance($distance);
            $em->persist($ride);
            $em->flush();

            //Actualizamos los puntos del usuario
			foreach ($users as $user) {
                $userChilds=$user->getChilds();
                $distance=0; 
                foreach ($userChilds as $userChild) {
                    $childrides = $em->getRepository("TrazeoBaseBundle:EChildRide")->findBy(array('updated' => false, 'child' => $userChild));
                    foreach ($childrides as $childride){
                        $distance=$childride->getDistance();
                        $output->writeln('<info>Niño participa en el paseo'. $userChild . '</info>');
                        //Añadimos los puntos obtenidos por que el niño participe en el paseo
                        $sopinetuserextend=$em->getRepository("SopinetUserBundle:SopinetUserExtend")->findOneByUser($user->getUser());
                        $sg->addUserAction(
                        "Child_On_Ride",
                        "TrazeoBaseBundle:EChild",
                        $childride->getChild()->getId(),
                        $user,
                        1,
                        false
                        );
                        //Añadimos los puntos obtenidos por la distancía recorrida por el niño  
                        $output->writeln('<info>Niño participa en el paseo '. $userChild . ' con puntos '. $distance.'</info>');
                        $sg->addUserAction(
                        "Distance_Points",
                        "TrazeoBaseBundle:EChild",
                        $childride->getChild()->getId(),
                        $user,
                        $distance,
                        false
                        );           
                        $childride->setUpdated(1);
                        $em->persist($childride);
                        $em->flush();
                    }
                }
            }
    	}
    }
}
    