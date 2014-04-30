<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity EPoints
 *
 * @ORM\Table("e_points")
 * @ORM\Entity
 */
class EPoints
{
	use \Sopinet\Bundle\SimplePointBundle\Model\GeoLocation;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/** @ORM\ManyToOne(targetEntity="ERoute", inversedBy="points")
	 */
	protected $route;
	
	/**
	 * @ORM\Column(name="pickup", type="boolean")
	 */
	protected $pickup;
	
	/**
	 * @ORM\Column(name="description", type="string", length=255)
	 */
	protected $description;

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
     * Set route
     *
     * @param \Trazeo\BaseBundle\Entity\ERoute $route
     *
     * @return EPoints
     */
    public function setRoute(\Trazeo\BaseBundle\Entity\ERoute $route = null)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return \Trazeo\BaseBundle\Entity\ERoute 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set pickup
     *
     * @param boolean $pickup
     *
     * @return EPoints
     */
    public function setPickup($pickup)
    {
        $this->pickup = $pickup;

        return $this;
    }

    /**
     * Get pickup
     *
     * @return boolean 
     */
    public function getPickup()
    {
        return $this->pickup;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return EPoints
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }
}
