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

    public function __construct()
    {
        parent::__construct();
    }
    
    
    /**
     * @ORM\OneToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="userextend")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;
    
    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Groups", inversedBy="userextendgroups")
     */
    private $groups;
    
    /** @ORM\OneToMany(targetEntity="Trazeo\BaseBundle\Entity\Groups", mappedBy="admin")
     */
    private $admingroup;
    
    /** @ORM\OneToMany(targetEntity="Trazeo\BaseBundle\Entity\Route", mappedBy="admin")
     */
    private $adminroutes;
    
    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Children", mappedBy="userextendchildren")
     */
    private $children;
    
    
    /** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\City", inversedBy="userextend")
     */
    private $city;
    
    /** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\Country", inversedBy="userextend")
     */
    private $country;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nick", type="string", length=255)
     */
    private $nick;


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
     * @param \Trazeo\BaseBundle\Entity\Groups $groups
     *
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
     * Add admingroup
     *
     * @param \Trazeo\BaseBundle\Entity\Groups $admingroup
     *
     * @return UserExtend
     */
    public function addAdmingroup(\Trazeo\BaseBundle\Entity\Groups $admingroup)
    {
        $this->admingroup[] = $admingroup;

        return $this;
    }

    /**
     * Remove admingroup
     *
     * @param \Trazeo\BaseBundle\Entity\Groups $admingroup
     */
    public function removeAdmingroup(\Trazeo\BaseBundle\Entity\Groups $admingroup)
    {
        $this->admingroup->removeElement($admingroup);
    }

    /**
     * Get admingroup
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdmingroup()
    {
        return $this->admingroup;
    }

    /**
     * Add adminroutes
     *
     * @param \Trazeo\BaseBundle\Entity\Route $adminroutes
     *
     * @return UserExtend
     */
    public function addAdminroute(\Trazeo\BaseBundle\Entity\Route $adminroutes)
    {
        $this->adminroutes[] = $adminroutes;

        return $this;
    }

    /**
     * Remove adminroutes
     *
     * @param \Trazeo\BaseBundle\Entity\Route $adminroutes
     */
    public function removeAdminroute(\Trazeo\BaseBundle\Entity\Route $adminroutes)
    {
        $this->adminroutes->removeElement($adminroutes);
    }

    /**
     * Get adminroutes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAdminroutes()
    {
        return $this->adminroutes;
    }

    /**
     * Add children
     *
     * @param \Trazeo\BaseBundle\Entity\Children $children
     *
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
}
