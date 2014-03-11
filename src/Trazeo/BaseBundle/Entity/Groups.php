<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Groups
 *
 * @ORM\Table()
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
     */
    protected $userExtendGroups;
    
    /** @ORM\ManyToOne(targetEntity="Trazeo\BaseBundle\Entity\UserExtend", inversedBy="admingroup")
     */
    protected $admin;
    
    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Children", inversedBy="groups")
     */
    protected $children;

    /** @ORM\ManyToOne(targetEntity="Trazeo\BaseBundle\Entity\Route", inversedBy="groups")
     */
    protected $routes;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     */
    protected $nombre;
    
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userExtendGroups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add userExtendGroups
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userExtendGroups
     * @return Groups
     */
    public function addUserExtendGroup(\Trazeo\BaseBundle\Entity\UserExtend $userExtendGroups)
    {
        $this->userExtendGroups[] = $userExtendGroups;

        return $this;
    }

    /**
     * Remove userExtendGroups
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userExtendGroups
     */
    public function removeUserExtendGroup(\Trazeo\BaseBundle\Entity\UserExtend $userExtendGroups)
    {
        $this->userExtendGroups->removeElement($userExtendGroups);
    }

    /**
     * Get userExtendGroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserExtendGroups()
    {
        return $this->userExtendGroups;
    }

    /**
     * Set admin
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $admin
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
     * @param \Trazeo\BaseBundle\Entity\Route $routes
     * @return Groups
     */
    public function setRoutes(\Trazeo\BaseBundle\Entity\Route $routes = null)
    {
        $this->routes = $routes;

        return $this;
    }

    /**
     * Get routes
     *
     * @return \Trazeo\BaseBundle\Entity\Route 
     */
    public function getRoutes()
    {
        return $this->routes;
    }
    
    public function __toString() {
    	return $this->getNombre();
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
}
