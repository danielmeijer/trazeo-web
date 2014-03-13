<?php

namespace Trazeo\BaseBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class UserExtend
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;   
    
    /**
     * @ORM\OneToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="userextend")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;
    
    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Groups", inversedBy="userextendgroups")
     */
    protected $groups;
    
    /** @ORM\OneToMany(targetEntity="Trazeo\BaseBundle\Entity\Groups", mappedBy="admin")
     */
    private $adminGroup;
    
    /** @ORM\OneToMany(targetEntity="Trazeo\BaseBundle\Entity\Routes", mappedBy="admin")
     */
    protected $adminRoutes;
    
    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Children", mappedBy="userExtendChildren")
     */
    protected $children;
    
    /** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\City", inversedBy="userextend")
     */
    protected $city;
    
    /** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\Country", inversedBy="userextend")
     */
    protected $country;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nick", type="string", length=255)
     */
    protected $nick;

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
     * Set nick
     *
     * @param string $nick
     * @return UserExtend
     */
    public function setNick($nick)
    {
        $this->nick = $nick;

        return $this;
    }

    /**
     * Get nick
     *
     * @return string 
     */
    public function getNick()
    {
        return $this->nick;
    }

    /**
     * Set user
     *
     * @param \Application\Sonata\UserBundle\Entity\User $user
     * @return UserExtend
     */
    public function setUser(\Application\Sonata\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add groups
     *
     * @param \Trazeo\BaseBundle\Entity\Groups $groups
     * @return UserExtend
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
     * Add adminGroup
     *
     * @param \Trazeo\BaseBundle\Entity\Groups $adminGroup
     * @return UserExtend
     */
    public function addAdminGroup(\Trazeo\BaseBundle\Entity\Groups $adminGroup)
    {
        $this->adminGroup[] = $adminGroup;

        return $this;
    }

    /**
     * Remove adminGroup
     *
     * @param \Trazeo\BaseBundle\Entity\Groups $adminGroup
     */
    public function removeAdminGroup(\Trazeo\BaseBundle\Entity\Groups $adminGroup)
    {
        $this->adminGroup->removeElement($adminGroup);
    }

    /**
     * Get adminGroup
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdminGroup()
    {
        return $this->adminGroup;
    }

    /**
     * Add adminRoutes
     *
     * @param \Trazeo\BaseBundle\Entity\Routes $adminRoutes
     * @return UserExtend
     */
    public function addAdminRoute(\Trazeo\BaseBundle\Entity\Routes $adminRoutes)
    {
        $this->adminRoutes[] = $adminRoutes;

        return $this;
    }

    /**
     * Remove adminRoutes
     *
     * @param \Trazeo\BaseBundle\Entity\Routes $adminRoutes
     */
    public function removeAdminRoute(\Trazeo\BaseBundle\Entity\Routes $adminRoutes)
    {
        $this->adminRoutes->removeElement($adminRoutes);
    }

    /**
     * Get adminRoutes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdminRoutes()
    {
        return $this->adminRoutes;
    }

    /**
     * Add children
     *
     * @param \Trazeo\BaseBundle\Entity\Children $children
     * @return UserExtend
     */
    public function addChild(\Trazeo\BaseBundle\Entity\Children $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Trazeo\BaseBundle\Entity\Children $children
     */
    public function removeChild(\Trazeo\BaseBundle\Entity\Children $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set city
     *
     * @param \JJs\Bundle\GeonamesBundle\Entity\City $city
     * @return UserExtend
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
     * @return UserExtend
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
    
    
    public function __toString() {
        return $this->getNick();
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->adminGroup = new \Doctrine\Common\Collections\ArrayCollection();
        $this->adminRoutes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

}