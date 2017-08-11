<?php

namespace Trazeo\MyPageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Entity Menu
 *
 * @ORM\Table("e_menu")
 * @ORM\Entity
 */
class Menu
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
     * @ORM\Column(name="title", type="string", length=255)
     *
     * @var string $title
     */
    protected $title;

    /**
     * @ORM\Column(name="link", type="string", length=255)
     *
     * @var string $link
     */
    protected $link;

    /** @ORM\ManyToOne(targetEntity="Page", inversedBy="menus")
     *  @ORM\JoinColumn(name="page_id", referencedColumnName="id")
     *
     *  @var Page
     */
    protected $page;

    /**
     * @ORM\OneToMany(targetEntity="Module", mappedBy="menu")
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $modules;

    /**
     * @ORM\Column(name="position", type="integer")
     * @var integer
     */
    protected $position;

    /**
     * @ORM\Column(name="color_back", type="string", length=6)
     *
     * @var string $color_back
     */
    protected $color_back;

    /**
     * @ORM\Column(name="color_front", type="string", length=6)
     *
     * @var string $color_front
     */
    protected $color_front;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modules = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Menu
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

    /**
     * Set position
     *
     * @param integer $position
     * @return Menu
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set page
     *
     * @param \Trazeo\MyPageBundle\Entity\Page $page
     * @return Menu
     */
    public function setPage(\Trazeo\MyPageBundle\Entity\Page $page = null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return \Trazeo\MyPageBundle\Entity\Page 
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Add modules
     *
     * @param \Trazeo\MyPageBundle\Entity\Module $modules
     * @return Menu
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
        $title = $this->getTitle();
        if ($this->getPage() != null) {
            $title .= " (".$this->getPage().")";
        }
        return $title;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return Menu
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }

    public function getClassCSS() {
        return "menu-" . $this->getLink();
    }

    /**
     * Set color_back
     *
     * @param string $colorBack
     * @return Menu
     */
    public function setColorBack($colorBack)
    {
        $this->color_back = $colorBack;

        return $this;
    }

    /**
     * Get color_back
     *
     * @return string 
     */
    public function getColorBack()
    {
        return $this->color_back;
    }

    /**
     * Set color_front
     *
     * @param string $colorFront
     * @return Menu
     */
    public function setColorFront($colorFront)
    {
        $this->color_front = $colorFront;

        return $this;
    }

    /**
     * Get color_front
     *
     * @return string 
     */
    public function getColorFront()
    {
        return $this->color_front;
    }
}
