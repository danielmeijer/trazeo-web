<?php
namespace Sopinet\Bundle\ChatBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Sopinet\Bundle\ChatBundle\Service\ApiHelper;
use Trazeo\BaseBundle\Entity\UserExtend as User;
use Trazeo\BaseBundle\Entity\UserExtend;
use Trazeo\BaseBundle\Entity\UserExtendRepository;

class ChatRepository extends EntityRepository
{
    /**
     * @param Integer $id
     *
     * @return bool
     */
    public function isNew($id)
    {
        $chat = $this->findOneById($id);

        if ($chat != null) {
            if ($chat->getId() === $id) {
                return false;
            }
        }

        return true;
    }

    /**
     * Función que comprueba si existe un chat
     *
     * @param Array $users
     *
     * @return boolean
     */
    public function checkChatExist($users)
    {
        $em = $this->getEntityManager();
        $repositoryChat = $em->getRepository('SopinetChatBundle:Chat');
        $chats=$repositoryChat->findAll();
        /** @var Chat $chat */
        foreach ($chats as $chat) {
            if ($users==$chat->getChatMembers()->toArray()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Función que modifica el Administrador de un Chat por otro
     *
     * @param Chat $chat
     * @param User $newAdminUser
     *
     * @throws \Exception
     *
     * @return bool
     */
    public function modifyAdmin(Chat $chat, User $newAdminUser)
    {
        $em = $this->getEntityManager();

        // Buscamos el antiguo administrador
        $oldAdminMember = $chat->getAdmin();
        // Si no encontramos administrador, devolvemos error
        if ($oldAdminMember == null) {
            throw new \Exception(ApiHelper::GENERALERROR);
        }
        // Si son el mismo, el antiguo administrador y el nuevo, devolvemos un true y no hacemos nada más
        if ($newAdminUser->getId() == $oldAdminMember->getId()) {
            return true;
        }

        // Se comprueba si el nuevo administrador es miembro del grupo
        if ($this->userInChat($newAdminUser, $chat)) {
            $chat->setAdmin($newAdminUser);
            $em->persist($chat);
            $em->flush();

            return true;
        } else {
            throw new \Exception(ApiHelper::USERNOTINCHAT);
        }
    }

    /**
     * Elimina a un usuario de un chat de tipo Evento
     * Devuelve true si se elimina con éxito
     * Devuelve false si no existe o el chat no es de tipo Event
     *
     * @param Chat $chat
     * @param Integer $userId - ID del usuario a eliminar del Chat
     *
     * @throws \Exception
     *
     * @return bool|Chat
     */
    public function removeMember(Chat $chat, $userId)
    {
        if ($chat->getType() != Chat::EVENT) {
            throw new \Exception(ApiHelper::CHATTYPEINCORRECT);
        }

        $em = $this->getEntityManager();

        /** @var UserExtendRepository $repositoryUser */
        $repositoryUser = $em->getRepository('TrazeoBaseBundle:UserExtend');

        /** @var UserExtend $user */
        $user = $repositoryUser->findOneById($userId);
        if ($user == null) {
            throw new \Exception(ApiHelper::USERNOTVALID);
        }
        if ($this->userInChat($user, $chat)) {
            $chat->removeChatMember($user);
            $user->removeChat($chat);
            $em->persist($chat);
            $em->persist($user);
            $em->flush();

            return $chat;
        }

        throw new \Exception(ApiHelper::USERNOTINCHAT);
    }

    /**
     * Añade un miembro a un chat Grupal
     *
     * @param Chat $chat - Entidad chat
     * @param Integer $userId - ID del usuario a introducir en el Chat
     *
     * @throws \Exception
     *
     * @return Chat|bool
     */
    public function addMember(Chat $chat, $userId)
    {
        $em = $this->getEntityManager();

        if (!$chat->isType(CHAT::EVENT)) {
            throw new \Exception(ApiHelper::CHATTYPEINCORRECT);
        }
        /** @var UserExtendRepository $repositoryUser */
        $repositoryUser = $em->getRepository('TrazeoBaseBundle:UserExtend');
        /** @var UserExtend $userToAdd */
        $userToAdd = $repositoryUser->findOneById($userId);

        // Comprobar que existe el usuario
        if (!$userToAdd) {
            throw new \Exception(ApiHelper::USERNOTVALID);
        }

        // Comprobar que no está ya en el chat
        if ($this->userInChat($userToAdd, $chat)) {
            $chat->removeChatMember($userToAdd);
            $userToAdd->removeChat($chat);
        }

        $chat->addChatMember($userToAdd);
        $userToAdd->addChat($chat);
        $em->persist($chat);
        $em->persist($userToAdd);
        $em->flush();

        return $chat;
    }

    /**
     * Marca un chat como borrado
     *
     * @param Chat $chat
     *
     * @return bool
     */
    public function deleteChat(Chat $chat)
    {
        $em = $this->getEntityManager();
        $em->remove($chat);
        $em->flush();

        return true;
    }

    /**
     * Devuelve los dispositivos de todos los usuarios
     * vinculados al Chat
     *
     * @param Integer $id
     *
     * @return array|bool
     */
    public function getDevices($id)
    {
        /* @var $chat Chat */
        $chat = $this->findOneById($id);

        if ($chat == null) {
            return false; // TODO: Devolver excepción en lugar de false?
        }

        $devices = array();
        foreach ($chat->getChatMembers() as $chatMember) {
            /* @var $chatMember UserExtend */
            // Pasamos los Devices a un Array
            $devicesArray = array();
            $devicesObject = $chatMember->getDevices();
            foreach ($devicesObject as $do) {
                $devicesArray[] = $do;
            }

            // Mezclamos con los que ya teníamos
            $devices = array_merge($devices, $devicesArray);
        }

        return $devices;
    }

    /**
     * Comprueba si un usuario esta dentro de un chat
     * @param $user
     * @param Chat $chat
     * @return bool
     */
    public function userInChat($user,Chat $chat)
    {
        return in_array($user, $chat->getChatMembers()->toArray());
    }
}
