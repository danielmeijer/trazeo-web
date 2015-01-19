<?php

namespace Trazeo\MyPageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Entity Page
 *
 * @ORM\Table("e_page")
 * @ORM\Entity
 */
class Page
{
    use ORMBehaviors\Timestampable\Timestampable;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="subdomain", type="string", length=255, nullable=true)
     */
    protected $subdomain;

    /** @ORM\ManyToOne(targetEntity="\Trazeo\BaseBundle\Entity\UserExtend", inversedBy="pageFront")
     *  @ORM\JoinColumn(name="userextend_id", referencedColumnName="id")
     *
     *  @var UserExtend
     */
    protected $userextend;

    /** @ORM\OneToMany(targetEntity="Menu", mappedBy="page")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $menus;

    /** @ORM\OneToMany(targetEntity="\Trazeo\BaseBundle\Entity\EGroup",  mappedBy="page")
     */
    protected $groups;


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
     * Set subdomain
     *
     * @param string $subdomain
     * @return Page
     */
    public function setSubdomain($subdomain)
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    /**
     * Get subdomain
     *
     * @return string 
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * Set userextend
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userextend
     * @return Page
     */
    public function setUserextend(\Trazeo\BaseBundle\Entity\UserExtend $userextend = null)
    {
        $this->userextend = $userextend;

        return $this;
    }

    /**
     * Get userextend
     *
     * @return \Trazeo\BaseBundle\Entity\UserExtend 
     */
    public function getUserextend()
    {
        return $this->userextend;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modules = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add modules
     *
     * @param \Trazeo\MyPageBundle\Entity\Module $modules
     * @return Page
     */
    public function addModule(\Trazeo\MyPageBundle\Entity\Module $modules)
    {
        $this->modules[] = $modules;

        return $this;
    }

    /**
     * Remove modules
     *
     * @param \Trazeo\MyPageBundle\Entity\Module $modules
     */
    public function removeModule(\Trazeo\MyPageBundle\Entity\Module $modules)
    {
        $this->modules->removeElement($modules);
    }

    /**
     * Get modules
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModules()
    {
        return $this->modules;
    }

    public function __toString() {
        return $this->getSubdomain();
    }

    /**
     * Add menus
     *
     * @param \Trazeo\MyPageBundle\Entity\Menu $menus
     * @return Page
     */
    public function addMenu(\Trazeo\MyPageBundle\Entity\Menu $menus)
    {
        $this->menus[] = $menus;

        return $this;
    }

    /**
     * Remove menus
     *
     * @param \Trazeo\MyPageBundle\Entity\Menu $menus
     */
    public function removeMenu(\Trazeo\MyPageBundle\Entity\Menu $menus)
    {
        $this->menus->removeElement($menus);
    }

    /**
     * Get menus
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMenus()
    {
        return $this->menus;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Page
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }
}
