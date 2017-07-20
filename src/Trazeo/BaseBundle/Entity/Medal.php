<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Table("e_medal")
 * @ORM\Entity()
 */
class Medal extends AbstractEntity
{
    public function __toString()
    {
        return $this->getTitle();
    }

    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="type", type="string")
     */
    protected $title;

    /**
     * @ORM\Column(name="description", type="string", length=1000)
     */
    protected $description;

    /**
     * @ORM\Column(name="image", type="string", length=500)
     */
    protected $image;

    /**
     * @ORM\ManyToMany(targetEntity="EChild", inversedBy="medals")
     * @ORM\JoinTable(name="medals_childs")
     *  @Exclude
     **/
    protected $childs;
    /**
     * Constructor
     */
    public function __construct()
    {
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
     * Set title
     *
     * @param string $title
     * @return Medal
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
     * Set description
     *
     * @param string $description
     * @return Medal
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set image
     *
     * @param string $image
     * @return Medal
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add childs
     *
     * @param \Trazeo\BaseBundle\Entity\EChild $childs
     * @return Medal
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
}
