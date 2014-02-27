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

    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\UserExtend", inversedBy="adminGroups")
     *  @ORM\JoinColumn(name="userExtend_groups", referencedColumnName="id")
     */
    private $userExtend;

    /** @ORM\ManyToOne(targetEntity="Trazeo\BaseBundle\Entity\UserExtend", inversedBy="groups") */
    private $admin;

    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Children", mappedBy="groups")
     *  @ORM\JoinColumn(name="children_groups", referencedColumnName="id")
     */
    private $children;

    /** @ORM\ManyToOne(targetEntity="Trazeo\BaseBundle\Entity\Route", inversedBy="groups") */
    private $route;


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
}

