<?php
namespace Sopinet\GCMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Sopinet\UserBundle\Model\BaseUser;

use Doctrine\ORM\Event\OnFlushEventArgs;
/**
* @ORM\Entity(repositoryClass="Sopinet\GCMBundle\Entity\DeviceRepository")
* @ORM\Table(name="gcm_device")
* @DoctrineAssert\UniqueEntity("id")
*/
class Device
{
    const IOS="iOS";
    const ANDROID="Android";

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sopinet\UserBundle\Entity\User", inversedBy="devices")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $user;
    /**
     * @ORM\Column(name="date_register", type="datetime")
     * @var \Date $date_register
     */
    protected $date_register;

    /**
     * @ORM\Column(name="token", type="string", length=1000)
     */
    protected $token;

    /**
     * @var string
     * iOS
     * Android
     * @ORM\Column(name="type", type="string", columnDefinition="enum('iOS','Android')")
     */
    protected $type;


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
     * Set dateRegister
     *
     * @param \DateTime $dateRegister
     *
     * @return Device
     */
    public function setDateRegister($dateRegister)
    {
        $this->date_register = $dateRegister;

        return $this;
    }

    /**
     * Get dateRegister
     *
     * @return \DateTime
     */
    public function getDateRegister()
    {
        return $this->date_register;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return Device
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }


    /**
     * Set type
     *
     * @param string $type
     *
     * @return Device
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
     * Set user
     *
     * @param BaseUser $user
     *
     * @return Device
     */
    public function setUser(\Application\Sopinet\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Sopinet\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function __toString() {
        return $this->getToken();
    }
}
