<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Route
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Route
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $admin;

    /**
     * @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\City")
     */
    
    private $city;
    
    /**
     * @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\Country")
     */
    
    protected $country;

    /**
     * @ORM\OneToMany(targetEntity="Application\Sonata\UserBundle\Entity\Group", mappedBy="Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    private $groups;


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
     * Get Admin
     *
     * @return string
     */
    public function getAdmin() {
    	return $this->Admin;
    }

    
    /**
     * Get City
     *
     * @return string
     */
    public function getCity() {
    	return $this->City;
    }
    
    
    /**
     * Get Country
     *
     * @return string
     */
    public function getCountry() {
    	return $this->Country;
    }
    
    
    /**
     * Get Groups
     *
     * @return string
     */
    public function getGroups() {
    	return $this->Groups;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set admin
     *
     * @param \Application\Sonata\UserBundle\Entity\User $admin
     *
     * @return Route
     */
    public function setAdmin(\Application\Sonata\UserBundle\Entity\User $admin = null)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Set city
     *
     * @param \JJs\Bundle\GeonamesBundle\Entity\City $city
     *
     * @return Route
     */
    public function setCity(\JJs\Bundle\GeonamesBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Set country
     *
     * @param \JJs\Bundle\GeonamesBundle\Entity\Country $country
     *
     * @return Route
     */
    public function setCountry(\JJs\Bundle\GeonamesBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Add groups
     *
     * @param \Application\Sonata\UserBundle\Entity\Group $groups
     *
     * @return Route
     */
    public function addGroup(\Application\Sonata\UserBundle\Entity\Group $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Application\Sonata\UserBundle\Entity\Group $groups
     */
    public function removeGroup(\Application\Sonata\UserBundle\Entity\Group $groups)
    {
        $this->groups->removeElement($groups);
    }
}
