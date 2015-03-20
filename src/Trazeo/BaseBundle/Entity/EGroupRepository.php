<?php
namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Sopinet\Bundle\ChatBundle\Entity\Chat;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

class EGroupRepository extends EntityRepository
{
    /**
     * remove user from group
     * @param Integer $groupId
     * @param UserExtend $user
     *
     * @throws PreconditionFailedHttpException
     * @throws \Exception
     */
    public function disjoinGroup($groupId,UserExtend $user)
    {
        $em = $this->getEntityManager();
        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($groupId);
        // Grupo eliminado
        if (!$group) {
            throw new PreconditionFailedHttpException('Group not found');
        }
         // usuario no es miembro del grupo
        if (!in_array($user, $group->getUserextendgroups()->toArray())) {
            throw new PreconditionFailedHttpException('User is not in the group');
        }
        // Solicitud del Administrador
        if ($group->getAdmin()->getId() == $user->getId()) {
            throw new PreconditionFailedHttpException('The admin cannot disjoin');
        }
        $repositoryChat = $em->getRepository('SopinetChatBundle:Chat');
        /** @var Chat $chat */
        //Si el usuario estaba en el chat del grupo tambien sale
        $chat=$group->getChat();
        if ($chat!=null) {
            if ($repositoryChat->userInChat($user, $chat)) {
                try {
                    $repositoryChat->removeMember($chat, $user->getId());
                } catch (\Exception $e) {
                    throw $e;
                }
            }
        }
        //Children disjoin on parent disjoin to group
        $childs=$user->getChilds();
        foreach ($childs as $child) {
            $group->removeChild($child);
        }
        $group->removeUserextendgroup($user);
        $em->persist($group);
        $em->flush();
    }

    /**
     * Add/remove a child to the group
     * @param Integer $group_id
     * @param Integer $child_id
     * @param UserExtend $user
     * @param Boolean $add
     */
    public function setChildOnGroup($group_id,$child_id,UserExtend $user,$add)
    {
        $em = $this->getEntityManager();

        $group = $em->getRepository('TrazeoBaseBundle:EGroup')->find($group_id);
        $child = $em->getRepository('TrazeoBaseBundle:EChild')->find($child_id);

        // Grupo eliminado
        if (!$group)throw new PreconditionFailedHttpException('Group not found');
        // el niño no existe
        if(!$child) throw new PreconditionFailedHttpException("The child doesn't exist");
        // el niño esta/no esta para añadir/remover del grupo
        if($add&&in_array($child,$group->getChilds()->toArray()))throw new PreconditionFailedHttpException('Child on group: '.$add);
        // el usuario no es tutor del niño
        if(!in_array($child,$user->getChilds()->toArray()))throw new PreconditionFailedHttpException("The parent is not the tutor");

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

    /**
     * Funcion que comprueba si un usuario esta en dentro de un grupo
     * @param UserExtend $userextend
     * @param EGroup $group
     *
     * @return bool
     */
    public function isUserInGroup(UserExtend $userextend,EGroup $group)
    {
        return in_array($userextend, $group->getUserextendgroups()->toArray());
    }

    /**
     * @param Integer $groupId
     * @param UserExtend $user
     */
    public function joinGroup($groupId, UserExtend $user)
    {
        $em=$this->getEntityManager();
        /** @var EGroup $group */
        $group=$this->find($groupId);
        if (!$group) {
            throw new Exception("Group doesn't exist");
        }
        $groupAdmin = $group->getAdmin();
        $groupVisibility = $group->getVisibility();
        //Comprobamos que el usuario no este ya en el grupo
        if ($this->isUserInGroup($user, $group)) {
            throw new Exception("User it's already on group");
        }
        //Comprobamos que el user tenga permisos
        if ($groupAdmin == $user || $groupVisibility == 0) {
            $group->addUserextendgroup($user);
            //Children autojoin on parent join to group
            $childs=$user->getChilds();
            foreach ($childs as $child) {
                $group->addChild($child);
            }
            //Si el chat de grupo existe se une al usuario
            /** @var Chat $chat */
            $chat=$group->getChat();
            if ($chat!=null) {
                $repositoryChat = $em->getRepository('SopinetChatBundle:Chat');
                try {
                    $repositoryChat->addMember($chat, $user->getId());
                } catch (\Exception $e) {
                    throw $e;
                }
            }
            $em->persist($group);
            $em->flush();
        } elseif ($groupVisibility == 1 ) {
            throw new Exception("The group is not public");
        } elseif ($groupVisibility == 2 ) {
            throw new Exception("The group is not public");
        }
    }
}