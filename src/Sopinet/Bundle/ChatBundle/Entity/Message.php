<?php
namespace Sopinet\Bundle\ChatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Exclude;
use Gedmo\Mapping\Annotation as Gedmo;
use Sopinet\GCMBundle as GCM;


/**
 * Entity Message
 *
 * @ORM\Table("e_message")
 * @ORM\Entity(repositoryClass="Sopinet\Bundle\ChatBundle\Entity\MessageRepository")
 */
class Message
{
    use GCM\Model\Message;
    use  ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Chat", inversedBy="messages", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $chat;

    /**
     * @ORM\ManyToOne(targetEntity="\Trazeo\BaseBundle\Entity\UserExtend", inversedBy="messages", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id" = "DESC"})
     * @Exclude
     */
    protected $user;


    /**
     * @ORM\ManyToOne(targetEntity="Sopinet\GCMBundle\Entity\Device", inversedBy="messages", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="device_id", referencedColumnName="id", nullable=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $device;

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
     * Set chat
     *
     * @param \Sopinet\Bundle\ChatBundle\Entity\Chat $chat
     * @return Message
     */
    public function setChat(\Sopinet\Bundle\ChatBundle\Entity\Chat $chat = null)
    {
        $this->chat = $chat;

        return $this;
    }

    /**
     * Get chat
     *
     * @return \Sopinet\Bundle\ChatBundle\Entity\Chat 
     */
    public function getChat()
    {
        return $this->chat;
    }

    /**
     * Set user
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $user
     * @return Message
     */
    public function setUser(\Trazeo\BaseBundle\Entity\UserExtend $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Trazeo\BaseBundle\Entity\UserExtend 
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * Set device
     *
     * @param \Sopinet\GCMBundle\Entity\Device $device
     * @return Message
     */
    public function setDevice(\Sopinet\GCMBundle\Entity\Device $device = null)
    {
        $this->device = $device;

        return $this;
    }

    /**
     * Get device
     *
     * @return \Sopinet\GCMBundle\Entity\Device 
     */
    public function getDevice()
    {
        return $this->device;
    }
}
