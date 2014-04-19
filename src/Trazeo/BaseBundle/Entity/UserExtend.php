<?php

namespace Trazeo\BaseBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("userextend")
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
    
    /** @ORM\ManyToMany(targetEntity="EGroup", mappedBy="userextendgroups")
     * @ORM\JoinColumn(name="groups_userextend", referencedColumnName="id")
     */
    protected $groups;
    
    /** @ORM\OneToMany(targetEntity="EGroup", mappedBy="admin")
     */
    private $adminGroups;
    
    /** @ORM\OneToMany(targetEntity="ERoute", mappedBy="admin")
     */
    protected $adminRoutes;
    
    /** @ORM\ManyToMany(targetEntity="EChild", mappedBy="userextendchilds")
     * @ORM\JoinColumn(name="childs_userextend", referencedColumnName="id")
     */
    protected $childs;
    
    /** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\City")
     */
    protected $city;
    
    /** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\Country")
     */
    protected $country;
    
    
    /** @ORM\OneToMany(targetEntity="EGroupAccess", mappedBy="userextend")
     */
    protected $access;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="nick", type="string", length=255)
     */
    protected $nick;
    
    public function __toString(){
    	if($this->nick == "")
    		return (string)$this->id;
    	return $this->nick;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->adminGroups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->adminRoutes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->childs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nick
     *
     * @param string $nick
     *
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
     *
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
     * @param \Trazeo\BaseBundle\Entity\EGroup $groups
     *
     * @return UserExtend
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
     * Add adminGroups
     *
     * @param \Trazeo\BaseBundle\Entity\EGroup $adminGroups
     *
     * @return UserExtend
     */
    public function addAdminGroup(\Trazeo\BaseBundle\Entity\EGroup $adminGroups)
    {
        $this->adminGroups[] = $adminGroups;

        return $this;
    }

    /**
     * Remove adminGroups
     *
     * @param \Trazeo\BaseBundle\Entity\EGroup $adminGroups
     */
    public function removeAdminGroup(\Trazeo\BaseBundle\Entity\EGroup $adminGroups)
    {
        $this->adminGroups->removeElement($adminGroups);
    }

    /**
     * Get adminGroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdminGroups()
    {
        return $this->adminGroups;
    }

    /**
     * Add adminRoutes
     *
     * @param \Trazeo\BaseBundle\Entity\ERoute $adminRoutes
     *
     * @return UserExtend
     */
    public function addAdminRoute(\Trazeo\BaseBundle\Entity\ERoute $adminRoutes)
    {
        $this->adminRoutes[] = $adminRoutes;

        return $this;
    }

    /**
     * Remove adminRoutes
     *
     * @param \Trazeo\BaseBundle\Entity\ERoute $adminRoutes
     */
    public function removeAdminRoute(\Trazeo\BaseBundle\Entity\ERoute $adminRoutes)
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
     * Add childs
     *
     * @param \Trazeo\BaseBundle\Entity\EChild $childs
     *
     * @return UserExtend
     */
    public function addChild(\Trazeo\BaseBundle\Entity\EChild $childs)
    {
        $this->childs[] = $childs;

        return $this;
    }

    /**
     * Remove childs
     *
     * @param \Trazeo\BaseBundle\Entity\EChild $childs
     */
    public function removeChild(\Trazeo\BaseBundle\Entity\EChild $childs)
    {
        $this->childs->removeElement($childs);
    }

    /**
     * Get childs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Set city
     *
     * @param \JJs\Bundle\GeonamesBundle\Entity\City $city
     *
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
     *
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


    /**
     * Add access
     *
     * @param \Trazeo\BaseBundle\Entity\EGroupAccess $access
     *
     * @return UserExtend
     */
    public function addAccess(\Trazeo\BaseBundle\Entity\EGroupAccess $access)
    {
        $this->access[] = $access;

        return $this;
    }

    /**
     * Remove access
     *
     * @param \Trazeo\BaseBundle\Entity\EGroupAccess $access
     */
    public function removeAccess(\Trazeo\BaseBundle\Entity\EGroupAccess $access)
    {
        $this->access->removeElement($access);
    }

    /**
     * Get access
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAccess()
    {
        return $this->access;
    }
}
