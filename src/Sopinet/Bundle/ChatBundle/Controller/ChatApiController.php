<?php

namespace Sopinet\Bundle\ChatBundle\Controller;

use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Post;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sopinet\Bundle\ChatBundle\Entity\Chat;
use Sopinet\Bundle\ChatBundle\Entity\ChatRepository;
use Sopinet\Bundle\ChatBundle\Entity\Message;
use Sopinet\Bundle\ChatBundle\Entity\MessageRepository;
use Sopinet\Bundle\ChatBundle\Service\ApiHelper;
use Sopinet\GCMBundle\Entity\Device;
use Sopinet\GCMBundle\Model\CustomMsg;
use Symfony\Component\HttpFoundation\Request;
use Sopinet\GCMBundle\Model\Msg;
use Sopinet\GCMBundle\Event\GCMEvent;
use Sopinet\GCMBundle\GCMEvents;
use FOS\RestBundle\Controller\Annotations as Rest;
use Trazeo\BaseBundle\Entity\UserExtend;


class ChatApiController extends FOSRestController{

    /**
     * @ApiDoc(
     *   description="Replica un mensaje de chat en el servidor. Se puede usar para recibir ficheros de tipo image, video, doc. En los ficheros se devolverá siempre la URL de los mismos en msg->text para los dispositivos receptores. En el caso de los ficheros de tipo video, se puede añadir _thumb.png tras la URL devuelta del vídeo y esto será el thumbnail del vídeo.",
     *   section="chat",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *      {"name"="text", "dataType"="string", "required"=true, "description"="Texto de mensaje en el chat"},
     *      {"name"="type", "dataType"="text|location|file|contact|remember", "required"=true, "description"="Tipo de mensaje"},
     *      {"name"="from", "dataType"="string", "required"=true, "description"="Registration_ID desde donde se envía el mensaje"},
     *      {"name"="chatid", "dataType"="string", "required"=true, "description"="ID del Chat en el servidor en el que se envía el mensaje"},
     *      {"name"="chattype", "dataType"="string", "required"=true, "description"="Indica si es bilateral o event"},
     *      {"name"="time", "dataType"="timestamp", "required"=true, "description"="Fecha y hora de envío de mensaje, formato Timestamp"},
     *      {"name"="msgid", "dataType"="string", "required"=false, "description"="ID del Chat en el servidor en el que se envía el mensaje"},
     *      {"name"="file", "dataType"="FILE",    "required"=false, "description"="Fichero normal en $_FILE"},
     *      {"name"="nobase64", "dataType"="string", "required"=false, "description"="Indica si el fichero se pasa en base64 o no"},
     *      {"name"="extension", "dataType"="string", "required"=false, "description"="En caso de no pasar el fichero en base64 se deberá de añadir la extensión"}
     *   }
     * )
     *
     * Función replicar un mensaje
     *
     * @Post("/reply")
     * @param Request $request
     */
    public function replyAction(Request $request){
        // Comprobamos Usuario
        $apiHelper=$this->get('apihelper');
        $user = $apiHelper->checkPrivateAccess($request);
        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getManager();
        //@var $user User
        if ($user === false) {
            $response = $apiHelper->msgDenied(ApiHelper::USERNOTVALID, 400);
            return $response;
        }

        // Construímos el MSG
        $msg = new Msg();
        // Si es de tipo file
        if($request->get('type') == "file" || $request->get('type')=="file_image" || $request->get("type") == "file_video" || $request->get("type") == "file_doc") {
            $em = $this->get('doctrine')->getManager();
            /* @var $repositoryFile FileRepository */
            $repositoryFile = $em->getRepository('PetyCashAppBundle:File');

            if ($request->get('type') == "file_doc") {
                $fileObject = $repositoryFile->uploadFileByFile($this->container, $request->files->get('file'), "doc");
                //$msg->text = $repositoryFile->uploadFileBytes($this->container, $request->get('file'), $request->get('extension'));
            } else if ($request->get('type') == 'file_video') {
                $fileObject = $repositoryFile->uploadFileByFile($this->container, $request->files->get('file'), "video");
                //$msg->text = $repositoryFile->uploadFileBytes($this->container, $request->get('file'), $request->get('extension'));
            } else {
                $fileObject = $repositoryFile->uploadFileByFile($this->container, $request->files->get('file'), "image");
                //Sistema antiguo: $msg->text = $repositoryFile->uploadFile($this->container, $request->get('file'));
            }

            $msg->text = $this->container->getParameter("image_url") . $fileObject->getName();
        }
        else {
            $msg->text = $request->get('text');
        }
        $msg->type = $request->get('type');
        $msg->from = $request->get('from');
        $msg->chatid = $request->get('chatid');
        $msg->chattype = $request->get('chattype');
        $msg->msgid = $request->get('msgid');
        $msg->time = $request->get('time');
        $msg->username=$user->__toString();
        $repositoryGroup = $em->getRepository('TrazeoBaseBundle:EGroup');
        $repositoryChat = $em->getRepository("SopinetChatBundle:Chat");
        $chat=$repositoryChat->find($msg->chatid);
        $group=$repositoryGroup->findOneByChat($chat);
        $msg->groupId=$group->getId();

        $logger = $this->get('logger');
        $logger->info("MSG: "+$msg->text+" - ");

        $repositoryDevice = $em->getRepository('SopinetGCMBundle:Device');
        /** @var Device $device */
        $device=$repositoryDevice->findOneByToken($msg->from);
        if($device==null)$apiHelper->msgDenied(ApiHelper::NODEVICE, 400);
        $msg->device=$device->getType();
        /** @var MessageRepository $repositoryMessage */
        $repositoryMessage = $em->getRepository('SopinetChatBundle:Message');
        /** @var Message $message */
        $repositoryMessage->addMsg($msg);

        // Obtenemos los devices del chatid
        $repositoryChat = $em->getRepository("SopinetChatBundle:Chat");
        $devices = $repositoryChat->getDevices($msg->chatid);

        // Si no hay dispositivos que notificar: salimos
        if (!is_array($devices)) return false;

        // Comprobamos permisos (el from tiene permiso para trabajar en el chatid)
        $ok = false;
        foreach($devices as $device) {
            /* @var $device Device */
            if ($device->getToken() == $msg->from) {
                $ok = true;
                // Obtenemos el número de teléfono y el usuario
                $msg->phone = $device->getUser()->getPhone();
            }
        }
        if (!$ok) return false; // Ha sucedido algo inesperado, ha mandado un mensaje alguien que no estaba en el Chat.

        // Enviamos el mensaje correspondiente a todos los dispositivos, excepto
        // al que está enviando este mensaje.
        foreach ($devices as $device) {
            if ($device->getToken() != $msg->from) {
                /* @var $container Container */
                $gcmhelper = $this->get('sopinet_gcmhelper');
                $msg->device=$device->getType();
                $gcmhelper->sendMessage($msg, $device->getToken());
            }
        }

        /** @var MessageRepository $repositoryMessage */
        $repositoryMessage = $em->getRepository("SopinetChatBundle:Message");
        $repositoryMessage->addMsg($msg);
        $response = $apiHelper->msgOK();
        return $response;
    }

    /**
     * @ApiDoc(
     *   description="Función para actualizar la imagen de un Evento",
     *   section="chat",
     *   parameters={
     *     {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *     {"name"="password", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *     {"name"="chatid", "dataType"="string", "required"=true, "description"="ID del chat"},
     *     {"name"="file", "dataType"="FILE", "required"=false, "description"="Fichero normal en $_FILE"}
     *   }
     * )
     *
     * Función para actualizar la imagen del chat
     * @param Request $request
     * @Post("/upgradeImage")
     */
    public function upgradeImageChatAction(Request $request) {
        $em = $this->get('doctrine')->getManager();

        /** @var ApiHelper $apiHelper */
        $apiHelper = $this->get('apihelper');

        // Cogemos el Chat
        $repositoryChat = $em->getRepository('PetyCashAppBundle:Chat');
        $chat = $repositoryChat->findOneById($request->get('chatid'));
        if ($chat == null) {
            $response = $apiHelper->msgDenied(ApiHelper::GENERALERROR, 400);
            return $response;
        }

        // Comprobamos que el usuario es administrador del Chat
        $user=$apiHelper->checkAdminAccess($request, $chat);
        /* @var $user User */
        if ($user === false) {
            $response = $apiHelper->msgDenied(ApiHelper::USERNOTADMIN, 400);
            return $response;
        }

        // Subimos la imagen
        /** @var FileRepository $repositoryFile */
        $repositoryFile = $em->getRepository('PetyCashAppBundle:File');
        $fileObject = $repositoryFile->uploadImageForChat($this->container, $request->files->get('file'), $chat);
        $url = $this->container->getParameter("image_url") . $fileObject->getName();

        // Devolvemos ok
        $response = $apiHelper->msgOK($url);
        return $response;
    }


    /**
     * @ApiDoc(
     *   description="Muestra los datos de un chat en el sistema",
     *   section="chat",
     *   parameters={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="ID del chat a rescatar"},
     *     {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *     {"name"="password", "dataType"="string", "required"=true, "description"="Password del usuario"}
     *   },
     *   output={
     *      "class"="Sopinet\Bundle\ChatBundle\Entity\Chat",
     *      "groups"="public"
     *   }
     * )
     *
     * Funcion para ver un chat
     * @Post("/get")
     */
    public function getAction(Request $request){
        $id = $request->get('id');
        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getManager();
        /** @var ChatRepository $repositoryChat */
        $repositoryChat = $em->getRepository('SopinetChatBundle:Chat');
        /** @var Chat $chat */
        $chat = $repositoryChat->find($id);

        // Comprobamos usuario
        $apiHelper = $this->get('apihelper');
        $user=$apiHelper->checkPrivateAccess($request);
        $userextend = $em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($user);#FIXME
        /* @var $user User */
        if ($user === false ) {
            $response = $apiHelper->msgDenied(ApiHelper::USERNOTVALID, 400);
            return $response;
        }
        elseif(!$repositoryChat->userInChat($userextend,$chat)){
            $response= $apiHelper->msgDenied(ApiHelper::USERNOTINCHAT, 400);
            return $response;
        }

        $apiHelper = $this->get('apihelper');
        $response = $apiHelper->msgOK($chat);
        return $response;
    }

    /**
     * @ApiDoc(
     *  description="Crear un nuevo Chat o CARGAR UNO EXISTENTE con los mismos miembros, si ya existe",
     *  section="chat",
     *  parameters={
     *      {"name"="chat[amount]", "dataType"="integer", "required"=true, "description"="Poner a 0, sólo para eventos"},
     *      {"name"="chat[type]", "dataType"="string", "required"=true, "description"="Poner a bilateral"},
     *      {"name"="chat[name]", "dataType"="string", "required"=true, "description"="Nombre del chat, por defecto será el nombre del usuario al que va dirigido"},
     *      {"name"="starter", "dataType"="string", "required"=true, "description"="ID del Usuario que inicia el Chat"},
     *      {"name"="user_id", "dataType"="string", "required"=true, "description"="ID del Segundo Usuario"}
     *  },
     *  output={
     *     "class"="PetyCash\AppBundle\Entity\Chat",
     *     "groups"={"public"}
     *  }
     * )
     *
     * Funcion para crear un chat bilateral o cargar uno existente con los mismos miembros, si ya existe
     * @Post("/newBilateral")
     */
    public function newBilateralAction(){
        $em = $this->get('doctrine')->getManager();
        $request = $this->get('request');
        $repositoryUser = $em->getRepository('ApplicationSopinetUserBundle:User');
        $repositoryChat = $em->getRepository('PetyCashAppBundle:Chat');
        $repositoryChatMember = $em->getRepository('PetyCashAppBundle:ChatMember');
        $apiHelper = $this->get('apihelper');

        // Comprobamos si el chat ya existe
        $members = array();
        $members[] = $request->get('starter');
        $members[] = $request->get('user_id');
        $idchat = $repositoryChat->checkChatExist($members, "bilateral");
        // Si ya existe lo cargamos y devolvemos
        if ($idchat != 0) {
            $chat = $repositoryChat->findOneById($idchat);
            $response = $apiHelper->msgOK($chat);
            return $response;
        }

        $chat = new Chat();
        $form = $this->createForm(new ChatType(), $chat);

        $form->handleRequest($request);

        if ($form->isValid()) {
            //Creamos el primer miembro
            $starter = $repositoryUser->findOneById($request->get('starter'));
            if(!$starter){
                $response = $apiHelper->msgDenied(ApiHelper::USERSTARTERNOTVALID, 400);
                return $response;
            }
            $repositoryChatMember->createNew($chat, $starter);

            //Creamos el segundo miembro
            $user = $repositoryUser->findOneById($request->get('user_id'));
            if(!$user){
                $response = $apiHelper->msgDenied(ApiHelper::USERNOTVALID, 400);
                return $response;
            }
            $repositoryChatMember->createNew($chat, $user);

            $em->persist($chat);
            $em->flush();

            $response = $apiHelper->msgOK($chat);
            return $response;
        }

        $response = $apiHelper->msgDenied($form->getErrorsAsString(), 400);
        return $response;
    }

    /**
     * @ApiDoc(
     *   description="Función para crear un chat de evento, también permite ingresar los usuarios al chat, directamente",
     *   section="chat",
     *   parameters={
     *      {"name"="chat[type]", "dataType"="string", "required"=true, "description"="Poner a event"},
     *      {"name"="chat[name]", "dataType"="string", "required"=true, "description"="Nombre del evento"},
     *      {"name"="admin", "dataType"="integer", "required"=true, "description"="ID del administrador del grupo"},
     *      {"name"="users", "dataType"="string", "required"=false, "description"="IDs de usuario separados por comas"}
     *   },
     *   output={
     *     "class"="Sopinet\Bundle\ChatBundle\Entity\Chat",
     *     "groups"={"public"}
     *   }
     * )
     *
     * Funcion para crear un chat grupal
     * @Post("/newEvent")
     */
    public function newEventAction(){
        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getManager();
        $request = $this->get('request');
        $repositoryUser = $em->getRepository('TrazeoBaseBundle:UserExtend');#FixMe
        $apiHelper = $this->get('apihelper');

        /** @var Chat $chat */
        $chat = new Chat();
        $form = $this->createForm(new ChatType(), $chat);

        $form->handleRequest($request);

        if ($form->isValid()) {
            //Creamos el administrador
            $admin = $repositoryUser->findOneById($request->get('admin'));
            if(!$admin){
                $response = $apiHelper->msgDenied(ApiHelper::USERNOTVALID, 400);
                return $response;
            }
            $chat->setAdmin($admin);
            $em->persist($chat);
            $em->flush();

            $users = $request->get('users');
            if ($users != "") {
                /** @var ChatRepository $repositoryChat */
                $repositoryChat = $em->getRepository('SopinetChatBundle:Chat');
                $users_array = explode(",", $users);
                foreach($users_array as $u) {
                    try{
                        $repositoryChat->addMember($chat, $u);
                    }
                    catch(\Exception $e){
                        return $apiHelper->exceptionHandler($e);
                    }
                }
            }


            $response = $apiHelper->msgOK($chat);
            return $response;
        }

        $response = $apiHelper->msgDenied($form->getErrorsAsString(), 400);
        return $response;
    }

    /**
     * @ApiDoc(
     *   description="Funcion para añadir un miembro a un chat de evento",
     *   section="chat",
     *   parameters={
     *      {"name"="chatid", "dataType"="integer", "required"=true, "description"="ID del chat"},
     *      {"name"="userid", "dataType"="integer", "required"=true, "description"="ID del usuario a invitar"},
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario logueado (debe ser administrador)"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Password del usuario logueado (debe ser administrador)"}
     *   }
     * )
     * @Post("/addMember")
     */
    public function addMemberAction(Request $request)
    {
        $em = $this->get('doctrine')->getManager();
        /** @var ApiHelper $apiHelper */
        $apiHelper = $this->get('apihelper');

        /** @var ChatRepository $repositoryChat */
        $repositoryChat = $em->getRepository('SopinetChatBundle:Chat');
        $chat = $repositoryChat->findOneById($request->get('chatid'));
        // Comprobamos que no sea null y que el tipo sea EVENT
        if ($chat == null || $chat->getType() != Chat::EVENT) {
            return $apiHelper->msgDenied(ApiHelper::CHATTYPEINCORRECT,400);
        }

        // Comprobamos que el usuario que invita es Administrador
        $userAdmin = $apiHelper->checkAdminAccess($request, $chat);
        /* @var $userAdmin User */
        if ($userAdmin === false) {
            return $apiHelper->msgDenied(ApiHelper::USERNOTADMIN,400);
        }
        try{
            $repositoryChat->addMember($chat, $request->get('userid'));
        }
        catch(\Exception $e){
            return $apiHelper->exceptionHandler($e);
        }

        $response = $apiHelper->msgOK($chat);
        return $response;
    }

    /**
     * @ApiDoc(
     *   description="Funcion para echar a un miembro de un chat de evento",
     *   section="chat",
     *   parameters={
     *      {"name"="chatid", "dataType"="integer", "required"=true, "description"="ID del chat"},
     *      {"name"="userid", "dataType"="integer", "required"=true, "description"="ID del usuario a echar"},
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario logueado (debe ser administrador)"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Password del usuario logueado (debe ser administrador)"}
     *   }
     * )
     * @Post("/kickMember")
     */
    public function kickMemberAction(Request $request) {
        /** @var EntityManager $em */
        $em = $this->get('doctrine')->getManager();
        /** @var ApiHelper $apiHelper */
        $apiHelper = $this->get('apihelper');

        /** @var ChatRepository $repositoryChat */
        $repositoryChat = $em->getRepository('SopinetChatBundle:Chat');
        $chat = $repositoryChat->findOneById($request->get('chatid'));
        // Comprobamos que no sea null y que el tipo sea EVENT
        if ($chat == null || $chat->getType() != Chat::EVENT) {
            return $apiHelper->msgDenied(ApiHelper::CHATTYPEINCORRECT,400);
        }

        $userAdmin = $apiHelper->checkAdminAccess($request, $chat);
        /* @var $userAdmin User */
        if ($userAdmin === false) {
            return $apiHelper->msgDenied(ApiHelper::USERNOTADMIN,400);
        }

        try{
            $repositoryChat->removeMember($chat, $request->get('userid'));
        }
        catch(\Exception $e){
            return $apiHelper->exceptionHandler($e);
        }
        $response = $apiHelper->msgOK($chat);
        return $response;
    }

    /**
     * @ApiDoc(
     *   description="(¡NO USAR!) Función que devuelve el listado de mensajes de un Chat",
     *   section="chat",
     *   parameters={
     *      {"name"="id", "dataType"="integer", "required"=true, "description"="ID del chat"},
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Password del usuario"}
     *   }
     * )
     *
     * @Post("/refreshMessages")
     */
    public function refreshMessagesAction(Request $request) {
        $em = $this->get('doctrine')->getManager();

        $repositoryChat = $em->getRepository('SopinetChatBundle:Chat');
        $repositoryMessage = $em->getRepository('SopinetChatBundle:Message');

        // Comprobamos usuario
        $apiHelper = $this->get('apihelper');
        $user=$apiHelper->checkPrivateAccess($request);
        /* @var $user User */
        if ($user === false) {
            $response = $apiHelper->msgDenied(ApiHelper::USERNOTVALID, 400);
            return $response;
        }

        /** @var Chat $chat */
        $chat = $repositoryChat->findOneById($request->get('id'));
        $messages = $repositoryMessage->findByChat($chat);

        /** @var ApiHelper $apiHelper */
        $apiHelper = $this->get('apihelper');
        $response = $apiHelper->msgByGroup($messages, array('resume'));
        return $response;
    }

    /**
     * @ApiDoc(
     *   description="Función que devuelve el listado de Chats de un usuario y sus datos resumen",
     *   section="chat",
     *   parameters={
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Password del usuario"}
     *   },
     *   output={
     *     "class"="array<PetyCash\AppBundle\Entity\Chat>",
     *     "groups"={"resume"}
     *   }
     * )
     *
     * @Post("/refreshList")
     */
    public function refreshListAction(Request $request) {
        $em = $this->get('doctrine')->getManager();

        // Comprobamos usuario
        // TODO: Comprobar el FROM
        $apiHelper = $this->get('apihelper');
        $user=$apiHelper->checkPrivateAccess($request);
        if ($user === false) {
            $response = $apiHelper->msgDenied(ApiHelper::USERNOTVALID, 400);
            return $response;
        }
        $chats=$user->getChats();
        $response = $apiHelper->msgByGroup($chats, array('resume'));
        return $response;
    }

    /**
     * @ApiDoc(
     *   description="Función que cambia el administrador de un Chat de tipo EVENT",
     *   section="chat",
     *   parameters={
     *      {"name"="chatid", "dataType"="integer", "required"=true, "description"="ID del chat (debe ser del tipo EVENT, sino dará un fallo)"},
     *      {"name"="adminid", "dataType"="integer", "required"=true, "description"="ID del nuevo usuario administrador"},
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *      {"name"="time", "dataType"="string", "required"=true, "description"="Timestamp del dispositivo que envía la petición"}
     *   }
     * )
     *
     * @Post("/changeAdministrator")
     */
    public function changeAdministratorAction(Request $request) {
        $em = $this->get('doctrine')->getManager();

        $apiHelper = $this->get('apihelper');

        // Cogemos el Chat
        /** @var ChatRepository $repositoryChat */
        $repositoryChat = $em->getRepository('SopinetChatBundle:Chat');
        /** @var Chat $chat */
        $chat = $repositoryChat->findOneById($request->get('chatid'));
        // Comprobamos que no sea null y que el tipo sea EVENT
        if ($chat == null || $chat->getType() != Chat::EVENT) {
            $response = $apiHelper->msgDenied(ApiHelper::GENERALERROR, 400);
            return $response;
        }

        // Comprobamos que el usuario es administrador del Chat
        $user=$apiHelper->checkAdminAccess($request, $chat);
        /* @var $user User */
        if ($user === false) {
            $response = $apiHelper->msgDenied(ApiHelper::USERNOTADMIN, 400);
            return $response;
        }

        /** @var UserRepository $repositoryUser */
        $repositoryUser = $em->getRepository('TrazeoBaseBundle:UserExtend');#FixMe
        $admin = $repositoryUser->findOneById($request->get('adminid'));
        if ($admin == null) {
            $response = $apiHelper->msgDenied(ApiHelper::GENERALERROR, 400);
            return $response;
        }

        // Modificamos el Administrador
        try{
            $repositoryChat->modifyAdmin($chat, $admin);
            $response = $apiHelper->msgOK();
            return $response;
        }
        catch(\Exception $e){
            return $apiHelper->exceptionHandler($e);
        }
    }

    /**
     * @ApiDoc(
     *   description="Función que deja un chat de tipo Evento",
     *   section="chat",
     *   parameters={
     *      {"name"="chatid", "dataType"="integer", "required"=true, "description"="ID del chat (debe ser del tipo EVENT, sino dará un fallo)"},
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Password del usuario"},
     *      {"name"="time", "dataType"="string", "required"=true, "description"="Timestamp del dispositivo que envía la petición"}
     *   }
     * )
     *
     * @Post("/leaveEvent")
     */
    public function leaveEventAction(Request $request) {
        $em = $this->get('doctrine')->getManager();

        /** @var ChatRepository $repositoryChat */
        $repositoryChat = $em->getRepository('SopinetChatBundle:Chat');

        // Comprobamos usuario
        $apiHelper = $this->get('apihelper');
        $user=$apiHelper->checkPrivateAccess($request);
        $repositoryUser = $em->getRepository('TrazeoBaseBundle:UserExtend');#FixMe
        $userextend = $repositoryUser->findOneByUser($user);
        /* @var $user User */
        if ($user === false) {
            $response = $apiHelper->msgDenied(ApiHelper::USERNOTVALID, 400);
            return $response;
        }

        /** @var Chat $chat */
        $chat = $repositoryChat->findOneById($request->get('chatid'));
        // Comprobamos que no sea null y que el tipo sea EVENT
        if ($chat == null || $chat->getType() != Chat::EVENT) {
            $response = $apiHelper->msgDenied(ApiHelper::GENERALERROR, 400);
            return $response;
        }
        try{
            $repositoryChat->removeMember($chat, $userextend);
        }
        catch(\Exception $e){
            return $apiHelper->exceptionHandler($e);
        }
        return $response = $apiHelper->msgOK();
    }

    /**
     * @ApiDoc(
     *   description="Función que elimina un chat de tipo Evento",
     *   section="chat",
     *   parameters={
     *      {"name"="chatid", "dataType"="integer", "required"=true, "description"="ID del chat (debe ser del tipo EVENT, sino dará un fallo)"},
     *      {"name"="email", "dataType"="string", "required"=true, "description"="Email del usuario administrador"},
     *      {"name"="password", "dataType"="string", "required"=true, "description"="Password del usuario administrador"},
     *      {"name"="time", "dataType"="string", "required"=true, "description"="Timestamp del dispositivo que envía la petición"}
     *   }
     * )
     *
     * @Post("/deleteEvent")
     */
    public function deleteEventAction(Request $request) {
        $em = $this->get('doctrine')->getManager();

        /** @var ChatRepository $repositoryChat */
        $repositoryChat = $em->getRepository('SopinetChatBundle:Chat');

        /** @var ApiHelper $apiHelper */
        $apiHelper = $this->get('apihelper');

        /** @var Chat $chat */
        $chat = $repositoryChat->findOneById($request->get('chatid'));
        // Comprobamos que no sea null y que el tipo sea EVENT
        if ($chat == null || $chat->getType() != Chat::EVENT) {
            $response = $apiHelper->msgDenied(ApiHelper::GENERALERROR, 400);
            return $response;
        }
        // Comprobamos usuario
        $user = $apiHelper->checkAdminAccess($request, $chat);
        /* @var $user User */
        if ($user === false) {
            $response = $apiHelper->msgDenied(ApiHelper::USERNOTVALID, 400);
            return $response;
        }

        if ($repositoryChat->deleteChat($chat)) {
            $response = $apiHelper->msgOK();
            return $response;
        } else {
            $response = $apiHelper->msgDenied(ApiHelper::GENERALERROR, 400);
            return $response;
        }

    }
}