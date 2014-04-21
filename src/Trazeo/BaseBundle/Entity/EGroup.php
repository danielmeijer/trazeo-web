<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity Group
 *
 * @ORM\Table("e_group")
 * @ORM\Entity
 */
class EGroup
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="UserExtend", inversedBy="groups")
     * @ORM\JoinTable(name="groups_userextend")
     **/
    protected $userextendgroups;
    
    /** @ORM\ManyToOne(targetEntity="UserExtend", inversedBy="adminGroups")
     */
    protected $admin;
    
    /**
     * @ORM\ManyToMany(targetEntity="EChild", inversedBy="groups")
     * @ORM\JoinTable(name="groups_childs")
     **/
    protected $childs;

    /** @ORM\ManyToOne(targetEntity="ERoute", inversedBy="groups")
     */
    protected $route;
    
    /**
     *
     * @ORM\Column(name="visibility", type="string", length=255, nullable=true)
     */
    protected $visibility;
    
    
    /** @ORM\OneToMany(targetEntity="EGroupAccess",  mappedBy="group")
     * @var unknown
     */
    protected $access;
    
    /** @ORM\OneToMany(targetEntity="EGroupInvite",  mappedBy="group")
     * @var unknown
     */
    protected $invite;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    public function __toString(){
    	if($this->name == "")
    		return (string)$this->id;
    	return $this->name;
    }

  
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userextendgroups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return EGroup
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
     * Add userextendgroups
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userextendgroups
     *
     * @return EGroup
     */
    public function addUserextendgroup(\Trazeo\BaseBundle\Entity\UserExtend $userextendgroups)
    {
        $this->userextendgroups[] = $userextendgroups;

        return $this;
    }

    /**
     * Remove userextendgroups
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userextendgroups
     */
    public function removeUserextendgroup(\Trazeo\BaseBundle\Entity\UserExtend $userextendgroups)
    {
        $this->userextendgroups->removeElement($userextendgroups);
    }

    /**
     * Get userextendgroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserextendgroups()
    {
        return $this->userextendgroups;
    }

    /**
     * Set admin
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $admin
     *
     * @return EGroup
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
     * Add childs
     *
     * @param \Trazeo\BaseBundle\Entity\EChild $childs
     *
     * @return EGroup
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
     * Set route
     *
     * @param \Trazeo\BaseBundle\Entity\ERoute $route
     *
     * @return EGroup
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
     * Set visibility
     *
     * @param boolean $visibility
     *
     * @return EGroup
     */
    public function setVisibility($visibility)
    {
    	$this->visibility = $visibility;
    
    	return $this;
    }
    
    /**
     * Get visibility
     *
     * @return boolean
     */
    public function getVisibility()
    {
    	return $this->visibility;
    }

    /**
     * Add access
     *
     * @param \Trazeo\BaseBundle\Entity\EGroupAccess $access
     *
     * @return EGroup
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

    /**
     * Add invite
     *
     * @param \Trazeo\BaseBundle\Entity\EGroupInvite $invite
     *
     * @return EGroup
     */
    public function addInvite(\Trazeo\BaseBundle\Entity\EGroupInvite $invite)
    {
        $this->invite[] = $invite;

        return $this;
    }

    /**
     * Remove invite
     *
     * @param \Trazeo\BaseBundle\Entity\EGroupInvite $invite
     */
    public function removeInvite(\Trazeo\BaseBundle\Entity\EGroupInvite $invite)
    {
        $this->invite->removeElement($invite);
    }

    /**
     * Get invite
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvite()
    {
        return $this->invite;
    }
}
