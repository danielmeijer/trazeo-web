<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;

/**
 * Entity EEvent
 *
 * @ORM\Table("e_event")
 * @ORM\Entity
 * 
 * @ExclusionPolicy("all")
 */
class EEvent
{
	use \Sopinet\Bundle\SimplePointBundle\Model\GeoLocation;
	use ORMBehaviors\Timestampable\Timestampable;
	/**
	 * @var integer
	 * 
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 * @Expose
	 */
	protected $id;
	
	/**
	 * @ORM\Column(name="action", type="string", length=50)
	 * @Expose
	 */
	protected $action; //point, in, out
	
	/**
	 * @ORM\Column(name="data", type="string", length=255)
	 * @Expose
	 */
	protected $data; //(x,y), child_id
	
	/**
	 * @ORM\ManyToOne(targetEntity="ERide", inversedBy="events")
	 * @ORM\JoinColumn(name="ride_id", referencedColumnName="id")
	 **/
	protected $ride;

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
     * Set action
     *
     * @param string $action
     *
     * @return EEvent
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return EEvent
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string 
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set ride
     *
     * @param \Trazeo\BaseBundle\Entity\ERide $ride
     *
     * @return EEvent
     */
    public function setRide(\Trazeo\BaseBundle\Entity\ERide $ride = null)
    {
        $this->ride = $ride;

        return $this;
    }

    /**
     * Get ride
     *
     * @return \Trazeo\BaseBundle\Entity\ERide 
     */
    public function getRide()
    {
        return $this->ride;
    }
}
