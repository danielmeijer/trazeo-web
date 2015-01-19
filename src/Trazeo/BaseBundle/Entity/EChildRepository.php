<?php
namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EChildRepository extends EntityRepository
{
    /**
     * Comprueba si un niño esta en un paseo
     * @param ERide $ride
     * @param EChild $child
     * @return bool
     */
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


    /**
     * Borramos un niño siempre y cuando el usuario sea el tutor del mismo
     * @param $id_child
     * @param $userextend
     * @throws AccessDeniedException
     */
    public function userDeleteChild($id_child,$userextend){
        $em=$this->getEntityManager();
        //Obtenemos el niño a borrar
        /** @var EChild $child */
        $child = $em->getRepository('TrazeoBaseBundle:EChild')->findOneById($id_child);
        $tutor= in_array($child, $userextend->getChilds()->toArray());

        //si no encontramos el niño
        if(!$child) throw new NotFoundHttpException("Child not found");
        //Si el usuario no es el tutor del niño y lo intenta borrar
        else if($tutor==false)throw new AccessDeniedException("User is not the child tutor");
        //Borramos el niño
        $em->remove($child);
        $em->flush();
    }
}