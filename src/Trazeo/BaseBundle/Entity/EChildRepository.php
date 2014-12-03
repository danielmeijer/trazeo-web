<?php
namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class EChildRepository extends EntityRepository
{
    public function isOnRide(ERide $ride,EChild $child){
        $reEvent=$this->getEntityManager()->getRepository('TrazeoBaseBundle:EEvent');
        $query = $reEvent->createQueryBuilder('e')
            ->where('e.data LIKE :name AND e.ride = :ride AND e.action = :in')
            ->setParameters(array('name' => '%'.$child->getNick()."%", 'ride' => $ride, 'in'=> 'in'))
            ->orderBy('e.createdAt', 'ASC')
            ->getQuery();

        $child=$query->getResult();
        return count($child)>0;
    }
}