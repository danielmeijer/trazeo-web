<?php
namespace Sopinet\Bundle\ChatBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\VirtualProperty;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Exclude;
use Gedmo\Mapping\Annotation as Gedmo;
use Trazeo\BaseBundle\Entity\EGroup;

/**
 * Entity Chat
 *
 * @ORM\Table("e_chat")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Entity(repositoryClass="Sopinet\Bundle\ChatBundle\Entity\ChatRepository")
 */
class Chat
{
    const EVENT = "event";
    const BILATERAL = "bilateral";
    const DISABLED = "disabled";

    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var \DateTime $deletedAt
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * Disabled -> Chat no finalizado de guardar en la base de datos
     * Bilateral -> Chat entre dos personas
     * Event -> Chat para un evento donde pueden participar dos o mas personas
     * Closed -> Chat que ha sido "eliminado" por el administrador(se marca como cerrado pero no se elimina)
     * @ORM\Column(name="type", type="string", columnDefinition="enum('disabled', 'bilateral', 'event')")
     */
    protected $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="\Trazeo\BaseBundle\Entity\UserExtend", mappedBy="chats")
     * @Exclude
     */
    protected $chatMembers;

    /** @ORM\OneToOne(targetEntity="Trazeo\BaseBundle\Entity\EGroup", inversedBy="chat")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * @Exclude
     */
    protected $group;

    /**
     * @ORM\ManyToOne(targetEntity="\Trazeo\BaseBundle\Entity\UserExtend", inversedBy="chatsOwned", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="admin_id", referencedColumnName="id", nullable=true)
     */
    protected $admin;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="chat", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id" = "DESC"})
     * @Exclude
     */
    protected $messages;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = "";
        $this->type = CHAT::DISABLED;
        $this->chatMembers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Funcion para comprobar si pertenece al tipo pasado por parametro
     * @param $type Tipo a comprobar
     * @return bool
     */
    public function isType($type){
        if($type === $this->type)
            return true;

        return false;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Chat
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Chat
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Add message
     *
     * @param Message $message
     *
     * @return Chat
     */
    public function addMessage(Message $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param Message $message
     */
    public function removeMessage(Message $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    public function __toString() {
        $return = "";
        if ($this != null) {
            if ($this->getType() == Chat::EVENT) {
                $return = $this->getName();
            } else if ($this->getType() == Chat::BILATERAL) {
                /** @var ChatMember $member */
                foreach ($this->getChatMembers() as $member) {
                    $return .= $member->__toString() . " ";
                }
            }
        }
        return $return;
    }

    /**
     * Devuelve el último mensaje del Chat
     */
    public function refreshLastMessage() {
        $this->last_message = $this->getMessages()[0];
        return $this->last_message;
    }

    /**
     * Si el chat en BILATERAL, devuelve el otro usuario
     * que no es el pasado por parámetro
     *
     * @param $user
     */
    public function getAnotherUser($user) {
        $another = null;
        if ($this->getType() == Chat::BILATERAL) {
            foreach($this->getChatMembers() as $member) {
                if ($member->getUser()->getId() != $user->getId()) {
                    $another = $member->getUser();
                }
            }
        }
        return $another;
    }


    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return Chat
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Add chatMembers
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $chatMembers
     * @return Chat
     */
    public function addChatMember(\Trazeo\BaseBundle\Entity\UserExtend $chatMembers)
    {
        $this->chatMembers[] = $chatMembers;

        return $this;
    }

    /**
     * Remove chatMembers
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $chatMembers
     */
    public function removeChatMember(\Trazeo\BaseBundle\Entity\UserExtend $chatMembers)
    {
        $this->chatMembers->removeElement($chatMembers);
    }

    /**
     * Get chatMembers
     *
     * @return \Doctrine\Common\Collections\Collection
     * @VirtualProperty
     * @SerializedName("chatMembers")
     */
    public function getChatMembers()
    {
        if ($this->getGroup()!=null) {
            return $this->getGroup()->getUserextendgroups();
        }

        return $this->chatMembers;
    }

    /**
     * Clean chatMembers
     *
     * @return Chat $this
     */
    public function cleanChatMembers()
    {
        $this->chatMembers=new ArrayCollection();

        return $this;
    }

    /**
     * Set admin
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $admin
     * @return Chat
     */
    public function setAdmin(\Trazeo\BaseBundle\Entity\UserExtend $admin = null)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return \Trazeo\BaseBundle\Entity\UserExtend
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * @param ArrayCollection $users
     *
     * @return $this
     */
    public function setChatMembers($users)
    {
        $this->chatMembers=$users;

        return $this;
    }

    /**
     * @return EGroup
     */
    public function getGroup(){
        return $this->group;
    }

    public function setGroup(EGroup $group)
    {
        $this->group=$group;

        return $this;
    }
}
