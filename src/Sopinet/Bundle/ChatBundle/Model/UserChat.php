<?php

namespace Sopinet\Bundle\ChatBundle\Model;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
/**
 * Message trait.
 *
 * Should be used inside entity, that needs to be one User for chats.
 */
trait UserChat
{

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
        $this->chats = new \Doctrine\Common\Collections\ArrayCollection();
        $this->chatsOwned = new \Doctrine\Common\Collections\ArrayCollection();
        $this->devices = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @ORM\OneToMany(targetEntity="\Sopinet\GCMBundle\Entity\Device", mappedBy="user")
     * @ORM\JoinColumn(name="device_id", referencedColumnName="id", nullable=true)
     */
    protected $devices;


    /**
     * @ORM\OneToMany(targetEntity="\Sopinet\Bundle\ChatBundle\Entity\Message", mappedBy="user")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $messages;

    /**
     * @ORM\ManyToMany(targetEntity="\Sopinet\Bundle\ChatBundle\Entity\Chat", inversedBy="chatMembers")
     * @ORM\JoinColumn(name="chat_id", referencedColumnName="id", nullable=true)
     */
    protected $chats;

    /**
     * @ORM\OneToMany(targetEntity="\Sopinet\Bundle\ChatBundle\Entity\Chat", mappedBy="admin")
     * @ORM\JoinColumn(name="chat_owned_id", referencedColumnName="id", nullable=true)
     */
    protected $chatsOwned;

    /**
     * Add message
     *
     * @param \Sopinet\Bundle\ChatBundle\Entity\Message $message
     *
     * @return User
     */
    public function addMessage(\Sopinet\Bundle\ChatBundle\Entity\Message $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param \Sopinet\Bundle\ChatBundle\Entity\Message $message
     */
    public function removeMessage(\Sopinet\Bundle\ChatBundle\Entity\Message $message)
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

    /**
     * Add device
     *
     * @param \Sopinet\GCMBundle\Entity\Device $device
     *
     * @return User
     */
    public function addDevice(\Sopinet\GCMBundle\Entity\Device $device)
    {
        $this->devices[] = $device;

        return $this;
    }

    /**
     * Remove message
     *
     * @param \Sopinet\GCMBundle\Entity\Device $device
     */
    public function removeDevice(\Sopinet\GCMBundle\Entity\Device $device)
    {
        $this->messages->removeElement($device);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDevices()
    {
        return $this->devices;
    }

    /**
     * Add chat
     *
     * @param \Sopinet\Bundle\ChatBundle\Entity\Chat $chat
     *
     * @return User
     */
    public function addChat(\Sopinet\Bundle\ChatBundle\Entity\Chat $chat)
    {
        $this->chats[] = $chat;

        return $this;
    }

    /**
     * Remove chat
     *
     * @param \Sopinet\Bundle\ChatBundle\Entity\Chat $chat
     */
    public function removeChat(\Sopinet\Bundle\ChatBundle\Entity\Chat $chat)
    {
        $this->chats->removeElement($chat);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChats()
    {
        return $this->chats;
    }

}