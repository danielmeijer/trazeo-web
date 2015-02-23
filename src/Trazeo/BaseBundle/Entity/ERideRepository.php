<?php
namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ERideRepository extends EntityRepository
{
    /**
     * Devuelve listado de objetos Niño en el Paseo
     *
     * @param ERide $ride
     * @return array
     */
    public function getChildrenInRide(ERide $ride) {
        // Si no tenemos niños, se revisa el paseo
        $em = $this->getEntityManager();

        $repositoryEChildRide = $em->getRepository('TrazeoBaseBundle:EChildRide');
        $childsInRide = $repositoryEChildRide->findBy(array('ride' => $ride->getId()));

        return $childsInRide;
    }

    /**
     * Devuelve el numero de participaciones en un Paseo
     * Comprueba previamente el campo FixChildCount que es usado para corregir errores
     *
     * @param ERide $ride
     * @return int
     */
    public function getFixCountChildrenInRide(ERide $ride) {
        if ($ride->getFixChildCount() != null) return $ride->getFixChildCount();
        return count($this->getChildrenInRide($ride));
    }
}