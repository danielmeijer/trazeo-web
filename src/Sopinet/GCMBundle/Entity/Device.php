<?php
namespace Sopinet\GCMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

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
     * @ORM\ManyToOne(targetEntity="\Trazeo\BaseBundle\Entity\UserExtend", inversedBy="devices")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
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
     * @ORM\Column(name="deviceId", type="string", length=1000)
     */
    protected $deviceId;

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

    public function __toString() {
        return $this->getToken();
    }

    /**
     * Set user
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $user
     * @return Device
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
     * Set device_id
     *
     * @param string $deviceId
     * @return Device
     */
    public function setDeviceId($deviceId)
    {
        $this->deviceId = $deviceId;

        return $this;
    }

    /**
     * Get device_id
     *
     * @return string 
     */
    public function getDeviceId()
    {
        return $this->deviceId;
    }
}
