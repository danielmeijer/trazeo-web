<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
/**
 * Entity EChildRide
 *
 * @ORM\Table("e_childride")
 * @ORM\Entity
 */
class EChildRide
{
	use ORMBehaviors\Timestampable\Timestampable;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** 
     * @ORM\ManyToOne(targetEntity="ERide")
     * @ORM\JoinColumn(name="ride_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $ride;
    
    /** 
     * @ORM\ManyToOne(targetEntity="EChild")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $child;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="distance", type="integer")
     *
     */
    protected $distance;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="updated", type="boolean")
     */
    protected $updated=false;

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
     * Set child
     *
     * @param \Trazeo\BaseBundle\Entity\EChild $child
     *
     * @return EChildInvite
     */
    public function setChild(\Trazeo\BaseBundle\Entity\EChild $child = null)
    {
        $this->child = $child;

        return $this;
    }

    /**
     * Get child
     *
     * @return \Trazeo\BaseBundle\Entity\EChild 
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Set distance
     *
     * @param integer $distance
     *
     * @return EChildRide
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return integer
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set ride
     *
     * @param \Trazeo\BaseBundle\Entity\ERide $ride
     *
     * @return EChildRide
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

    /**
     * Set updated
     *
     * @param boolean $updated
     *
     * @return EChildRide
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return boolean
     */
    public function getUpdated()
    {
        return $this->updated;
    }
}
