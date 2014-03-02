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
    private $id;

    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\UserExtend", mappedBy="groups")
     */
    private $userextendgroups;
    
    /** @ORM\ManyToOne(targetEntity="Trazeo\BaseBundle\Entity\UserExtend", inversedBy="admingroup")
     */
    private $admin;
    
    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Children", inversedBy="groups")
     */
    private $children;

    /** @ORM\ManyToOne(targetEntity="Trazeo\BaseBundle\Entity\Route", inversedBy="groups")
     */
    private $routes;

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
     * Set userExtend
     *
     * @param string $userExtend
     *
     * @return Groups
     */
    public function setUserExtend($userExtend)
    {
        $this->userExtend = $userExtend;

        return $this;
    }

    /**
     * Get userExtend
     *
     * @return string 
     */
    public function getUserExtend()
    {
        return $this->userExtend;
    }

    /**
     * Set admin
     *
     * @param string $admin
     *
     * @return Groups
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return string 
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set children
     *
     * @param string $children
     *
     * @return Groups
     */
    public function setChildren($children)
    {
        $this->children = $children;

        return $this;
    }

    /**
     * Get children
     *
     * @return string 
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Set route
     *
     * @param string $route
     *
     * @return Groups
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userextend = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add userextend
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userextend
     *
     * @return Groups
     */
    public function addUserextend(\Trazeo\BaseBundle\Entity\UserExtend $userextend)
    {
        $this->userextend[] = $userextend;

        return $this;
    }

    /**
     * Remove userextend
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userextend
     */
    public function removeUserextend(\Trazeo\BaseBundle\Entity\UserExtend $userextend)
    {
        $this->userextend->removeElement($userextend);
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
     * Set routes
     *
     * @param \Trazeo\BaseBundle\Entity\Route $routes
     *
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
}
