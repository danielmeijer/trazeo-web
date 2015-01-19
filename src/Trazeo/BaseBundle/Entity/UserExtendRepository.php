<?php
namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Trazeo\BaseBundle\Entity\ERide;
use Trazeo\BaseBundle\Entity\EChild;
use Trazeo\BaseBundle\Entity\UserExtend;

class UserExtendRepository extends EntityRepository
{
    public function hasChildOnRide(UserExtend $user,ERide $ride){
        $userChilds=$user->getChilds();
        foreach ($userChilds as $child){
            $repositoryChild=$this->getEntityManager()->getRepository('TrazeoBaseBundle:EChild');
            if($repositoryChild->isOnRide($ride,$child))return true;
        }
        return false;
    }

    public function getCurrentPoints(UserExtend $user){
        $points=$user->getPoints()-$user->getSpendedPoints();
        return $points;
    }
}