<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Groups
 *
 * @ORM\Table("groups")
 * @ORM\Entity
 */
class Groups
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\UserExtend", mappedBy="groups")
     * @ORM\JoinColumn(name="userextend_groups", referencedColumnName="id")
     */
    protected $userextendgroups;
    
    /** @ORM\ManyToOne(targetEntity="Trazeo\BaseBundle\Entity\UserExtend", inversedBy="adminGroup")
     */
    protected $admin;
    
    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Children", inversedBy="groups")
     * @ORM\JoinColumn(name="groups_children", referencedColumnName="id")
     */
    protected $children;

    /** @ORM\ManyToOne(targetEntity="Trazeo\BaseBundle\Entity\Routes", inversedBy="groups")
     */
    protected $routes;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userExtendGroups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }   

    public function __toString(){
    	if($this->name == "")
    		return (string)$this->id;
    	return $this->name;
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
     * @return Groups
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
     * Add userextendgroups
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userextendgroups
     *
     * @return Groups
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
     * @return Groups
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
     * Add children
     *
     * @param \Trazeo\BaseBundle\Entity\Children $children
     *
     * @return Groups
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
     * Set routes
     *
     * @param \Trazeo\BaseBundle\Entity\Routes $routes
     *
     * @return Groups
     */
    public function setRoutes(\Trazeo\BaseBundle\Entity\Routes $routes = null)
    {
        $this->routes = $routes;

        return $this;
    }

    /**
     * Get routes
     *
     * @return \Trazeo\BaseBundle\Entity\Routes 
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Groups
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
}
