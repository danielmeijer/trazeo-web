<?php
namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ERideRepository extends EntityRepository
{
    public function getChildrenInRide(ERide $ride) {
        // Si no tenemos niños, se revisa el paseo
        $em = $this->getEntityManager();

        $repositoryEChildRide = $em->getRepository('TrazeoBaseBundle:EChildRide');
        $childsInRide = $repositoryEChildRide->findBy(array('ride' => $ride->getId()));

        return $childsInRide;
    }
}