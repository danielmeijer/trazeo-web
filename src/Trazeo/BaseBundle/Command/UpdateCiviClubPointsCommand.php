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

class UpdateCiviClubPointsCommand extends ContainerAwareCommand
{
	# php app/console trazeo:civipoints
	
    protected function configure()
    {
        $this
            ->setName('trazeo:civipoints')
            ->setDescription('Calculate points for Users')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $actios_allowed=['Child_On_Ride','Distance_Points','Manage_Ride'];
        $con = $this->getContainer();
        $con->enterScope('request');
        $con->set('request', new Request(), 'request');
        $em  = $con->get('doctrine')->getManager();
        $civiclub= $con->get('sopinet_gamification_civiclub');
        $gamification= $con->get('sopinet_gamification');

        $users = $em->getRepository("SopinetUserBundle:SopinetUserExtend")->findAll();
        $reUserValue = $em->getRepository("SopinetUserPreferencesBundle:UserValue");
        $reUserActions=$em->getRepository("SopinetGamificationBundle:EUserAction");
        foreach ($reUserActions->findByUpdated(null) as $action){
            $action->setUpdated(false);
        }
        $reUserSetting = $em->getRepository("SopinetUserPreferencesBundle:UserSetting");
        $civiclub_setting=$reUserSetting->findOneByName('civiclub_conexion');
 
        //Actualizamos los puntos del usuario
        foreach ($users as $user) {
            $actions=$reUserActions->findBy(array('sopinetuserextends'=>$user,'updated'=>0));
            $user = $em->getRepository("SopinetUserBundle:SopinetUserExtend")->findOneByUser($user->getUser());
            if(count($actions)!=0){
                $points=0;
                foreach ($actions as $action) {
                    ld($action->getActions()->getName());
                    if($action->getActions()!=null && in_array($action->getActions()->getName(), $actios_allowed)){
                        if($action->getLastupdate()==0){
                            $val=ceil($action->getActions()->getPoints()*$action->getAcumulated());
                            $action->setLastupdate($val);
                            $points+=$val;
                        }
                    else{
                            $val=ceil($action->getActions()->getPoints()*$action->getAcumulated())-($action->getLastupdate());
                            $action->setLastupdate($action->getLastupdate()+$val);
                            $points+=$val;                        
                        }
                    }
                    elseif($action->getSequences()!=null && in_array($action->getActions()->getName(), $actios_allowed)){
                        $points+=$action->getSequence()->getPoints();
                    }
                    $action->setUpdated(true);
                    $em->persist($action);
                    $em->flush();
                }

                $value=$reUserValue->getValue($user, $civiclub_setting);
                if($value=='yes'){
                    $civiclub->civiclubCall($user,$points);             
                }
            }
        }
    }
}