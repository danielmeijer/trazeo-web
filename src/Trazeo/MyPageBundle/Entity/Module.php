<?php

namespace Trazeo\MyPageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Entity Module
 *
 * @ORM\Table("e_module")
 * @ORM\Entity
 */
class Module
{
    const TYPE_IMAGE = "Image";
    const TYPE_HTML = "Html";
    const TYPE_COMBOBIGINTRO = "ComboBigIntro";
    const TYPE_TRAZEOROUTES = "TrazeoRoutes";

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
     * @ORM\Column(name="content", type="string", length=5000)
     *
     * @var string $content
     */
    protected $content;

    /**
     * @ORM\Column(name="type", type="string", length=30)
     * @var string $type
     */
    protected $type;

    /**
     * @ORM\Column(name="position", type="integer")
     * @var integer
     */
    protected $position;

    /** @ORM\ManyToOne(targetEntity="Menu", inversedBy="modules")
     *  @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
     *
     *  @var Menu
     */
    protected $menu;

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
     * @return Module
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
     * Set content
     *
     * @param string $content
     * @return Module
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string 
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Module
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Module
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
     * @return Module
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

    public function getTypeComplete() {
        return "\Trazeo\MyPageBundle\Classes\Module" . "\\" . $this->getType();
    }

    public function getClass()
    {
        $class_name = $this->getTypeComplete();
        $class_temp = new $class_name;
        return $class_temp;
    }

    /**
     * Set menu
     *
     * @param \Trazeo\MyPageBundle\Entity\Menu $menu
     * @return Module
     */
    public function setMenu(\Trazeo\MyPageBundle\Entity\Menu $menu = null)
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get menu
     *
     * @return \Trazeo\MyPageBundle\Entity\Menu 
     */
    public function getMenu()
    {
        return $this->menu;
    }

    public function __toString() {
        return $this->getTitle();
    }
}
