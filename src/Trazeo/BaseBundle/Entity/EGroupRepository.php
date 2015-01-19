<?php
namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class EGroupRepository extends EntityRepository
{
    /**
     * remove user from group
     * @param $group_id
     * @param $user
     * @throws PreconditionFailedHttpException
     */
    public function disjoinGroup($group_id,$user){
        $em = $this->getEntityManager();

        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($group_id);


        // Grupo eliminado
        if (!$group)throw new PreconditionFailedHttpException('Group not found');
         // usuario no es miembro del grupo
        if  (!in_array($user,$group->getUserextendgroups()->toArray()))throw new PreconditionFailedHttpException('User is not in the group');
        // Solicitud del Administrador
        if ($group->getAdmin()->getId() == $user->getId())throw new PreconditionFailedHttpException('The admin cannot disjoin');

        $group->removeUserextendgroup($user);
        $em->persist($group);

        //Children disjoin on parent disjoin to group
        $childs=$user->getChilds();
        foreach($childs as $child){
            $group->removeChild($child);
        }
        $em->persist($group);
        $em->flush();
    }

    /**
     * Add/remove a child to the group
     * @param $group_id
     * @param $child_id
     * @param $user
     * @param $add
     */
    public function setChildOnGroup($group_id,$child_id,$user,$add){
        $em = $this->getEntityManager();

        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($group_id);
        $child = $em->getRepository('TrazeoBaseBundle:EChild')->find($child_id);

        // Grupo eliminado
        if (!$group)throw new PreconditionFailedHttpException('Group not found');
        // el niño esta/no esta para añadir/remover del grupo
        if($add==in_array($child,$group->getUserextendgroups()->toArray()))throw new PreconditionFailedHttpException('Child on group: '.$add);
        // el niño no existe
        if(!$child) throw new PreconditionFailedHttpException("The child doesn't exist");
        // el usuario no es tutor del niño
        if(!in_array($child,$user->getChilds()->toArry()))throw new PreconditionFailedHttpException("The parent is not the tutor");

        if($add)$group->addChild($child);
        else $group->removeChild($child);
        $em->persist($group);
        $em->flush();
    }

    /**
     * set country from string
     * @param $group_id
     * @param $country_name
     */
    public function setCountry($group_id,$country_name){
        $em = $this->getEntityManager();

        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($group_id);
        $country= $em->getRepository('JJsGeonamesBundle:Country')->findOneByName($country_name);

        // el pais no existe
        if(!$country) throw new PreconditionFailedHttpException("Country not found");

        $group->setCountry($country);
        $em->persist($group);
        $em->flush();
    }


    /**
     * If user is the admin of the group it will be deleted
     * @param $group_id
     * @param $userextend
     * @throws PreconditionFailedHttpException
     * @throws OperationNotPermitedException
     */
    public function userDeleteGroup($group_id,$userextend){
        $em = $this->getEntityManager();

        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($group_id);
        // el grupo no existe
        if(!$group) throw new PreconditionFailedHttpException("Group not found");
        // el usuario no es el admin
        else if($group->getAdmin()!=$userextend) throw new AccessDeniedException("User is not the admin");

        $em->remove($group);
        $em->flush();
    }


}