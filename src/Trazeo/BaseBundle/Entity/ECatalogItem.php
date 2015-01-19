<?php
 
namespace Trazeo\BaseBundle\Entity;

 use Doctrine\ORM\Mapping as ORM;
 use Knp\DoctrineBehaviors\Model as ORMBehaviors;

 /**
 * Entity ECatalogItem
 *
 * @ORM\Table("e_catalogitem")
 * @ORM\Entity
 */
 class ECatalogItem
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
	 * @var integer
	 *
	 * @ORM\Column(name="points", type="integer")
	 */
	 protected $points=0;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer")
     */
     protected $position=0;
     
	/**
	 * @var string
	 *
	 * @ORM\Column(name="company", type="string")
	 */
	 protected $company="";

	/**
	 * @var string
	 *
	 * @ORM\Column(name="title", type="string")
	 */
	 protected $title="";

	/**
	 * @var text
	 *
	 * @ORM\Column(name="description", type="text")
	 */
	 protected $description="";

	/**
	 * @var string
	 *
	 * @ORM\Column(name="link", type="string")
	 */
	 protected $link="";

    /**
     * @var boolean
     *
     * @ORM\Column(name="complete", type="boolean")
     */
     protected $complete=0;

	/**
	 * @ORM\OneToMany(targetEntity="File", mappedBy="catalogitems")
	 */
	 protected $file;

	/**
	 * @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\City", inversedBy="catalogitem")
     * @ORM\JoinColumn(name="citys_id", referencedColumnName="id", nullable=true)
	 */
	 protected $citys;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->file = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set company
     *
     * @param string $company
     *
     * @return ECatalogItem
     */
    public function setCompany($company)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return ECatalogItem
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
     *
     * @return ECatalogItem
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
     * Set link
     *
     * @param string $link
     *
     * @return ECatalogItem
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

    /**
     * Add file
     *
     * @param \Trazeo\BaseBundle\Entity\File $file
     *
     * @return ECatalogItem
     */
    public function addFile(\Trazeo\BaseBundle\Entity\File $file)
    {
        $this->file[] = $file;

        return $this;
    }

    /**
     * Remove file
     *
     * @param \Trazeo\BaseBundle\Entity\File $file
     */
    public function removeFile(\Trazeo\BaseBundle\Entity\File $file)
    {
        $this->file->removeElement($file);
    }

    /**
     * Get file
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Set citys
     *
     * @param \JJs\Bundle\GeonamesBundle\Entity\City $citys
     *
     * @return ECatalogItem
     */
    public function setCitys(\JJs\Bundle\GeonamesBundle\Entity\City $citys = null)
    {
        $this->citys = $citys;

        return $this;
    }

    /**
     * Get citys
     *
     * @return \JJs\Bundle\GeonamesBundle\Entity\City
     */
    public function getCitys()
    {
        return $this->citys;
    }

    /**
     * Set points
     *
     * @param integer $points
     *
     * @return ECatalogItem
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get points
     *
     * @return integer
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set complete
     *
     * @param boolean $complete
     *
     * @return ECatalogItem
     */
    public function setComplete($complete)
    {
        $this->complete = $complete;

        return $this;
    }

    /**
     * Get complete
     *
     * @return boolean
     */
    public function getComplete()
    {
        return $this->complete;
    }

    /**
     * Set order
     *
     * @param integer $order
     *
     * @return ECatalogItem
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return ECatalogItem
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
}
