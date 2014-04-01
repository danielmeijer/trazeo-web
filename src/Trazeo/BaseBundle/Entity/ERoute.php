<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity Route
 *
 * @ORM\Table("e_route")
 * @ORM\Entity
 */
class ERoute
{
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/** @ORM\OneToMany(targetEntity="EGroup", mappedBy="route")
	 */
	protected $groups;
	
	/** @ORM\ManyToOne(targetEntity="UserExtend", inversedBy="adminRoutes")
	 */
	protected $admin;
	
	/** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\City")
	 */
	protected $city;
	
	/** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\Country")
	 */
	protected $country;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	protected $name;
	
	public function __toString() {
		
		return $this->getName();
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
     * Set name
     *
     * @param string $name
     *
     * @return ERoute
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
     * Add groups
     *
     * @param \Trazeo\BaseBundle\Entity\EGroup $groups
     *
     * @return ERoute
     */
    public function addGroup(\Trazeo\BaseBundle\Entity\EGroup $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Trazeo\BaseBundle\Entity\EGroup $groups
     */
    public function removeGroup(\Trazeo\BaseBundle\Entity\EGroup $groups)
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
     * @return ERoute
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
     * @return ERoute
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
     * @return ERoute
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
