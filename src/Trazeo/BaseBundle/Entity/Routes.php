<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Routes
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Routes
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/** @ORM\OneToMany(targetEntity="Trazeo\BaseBundle\Entity\Groups", mappedBy="routes")
	 */
	protected $groups;
	
	/** @ORM\ManyToOne(targetEntity="Trazeo\BaseBundle\Entity\UserExtend", inversedBy="adminRoutes")
	 */
	protected $admin;
	
	/** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\City", inversedBy="routes")
	 */
	protected $city;
	
	/** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\Country", inversedBy="routes")
	 */
	protected $country;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="nombre", type="string", length=255)
	 */
	protected $nombre;
	
	public function __toString() {
		
		return $this->getNombre();
	}
	
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nombre
     *
     * @param string $nombre
     *
     * @return Routes
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;

        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Add groups
     *
     * @param \Trazeo\BaseBundle\Entity\Groups $groups
     *
     * @return Routes
     */
    public function addGroup(\Trazeo\BaseBundle\Entity\Groups $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Trazeo\BaseBundle\Entity\Groups $groups
     */
    public function removeGroup(\Trazeo\BaseBundle\Entity\Groups $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set admin
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $admin
     *
     * @return Routes
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
     * Set city
     *
     * @param \JJs\Bundle\GeonamesBundle\Entity\City $city
     *
     * @return Routes
     */
    public function setCity(\JJs\Bundle\GeonamesBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \JJs\Bundle\GeonamesBundle\Entity\City 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param \JJs\Bundle\GeonamesBundle\Entity\Country $country
     *
     * @return Routes
     */
    public function setCountry(\JJs\Bundle\GeonamesBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \JJs\Bundle\GeonamesBundle\Entity\Country 
     */
    public function getCountry()
    {
        return $this->country;
    }
    
}
