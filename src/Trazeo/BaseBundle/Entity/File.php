<?php
 namespace Trazeo\BaseBundle\Entity;

 use Doctrine\ORM\Mapping as ORM;
 use JMS\Serializer\Annotation\SerializedName;
 use JMS\Serializer\Annotation\Type;
 use JMS\Serializer\Annotation\VirtualProperty;
 use Knp\DoctrineBehaviors\Model as ORMBehaviors;

 /**
 * Entity File
 *
 * @ORM\Table("e_file")
 * @ORM\Entity
 */
 class File
 {
	 use \Sopinet\Bundle\UploadMagicBundle\Model\UploadMagic;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	 protected $id;

	/**
	 * @ORM\OneToOne(targetEntity="ECatalogItem")
	 */
	 protected $catalogitems;

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
     * Set mimeType
     *
     * @param string $mimeType
     *
     * @return File
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * Get mimeType
     *
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * Set catalogitems
     *
     * @param \Trazeo\BaseBundle\Entity\ECatalogItem $catalogitems
     *
     * @return File
     */
    public function setECatalogItem(\Trazeo\BaseBundle\Entity\ECatalogItem $catalogitems = null)
    {
        $this->catalogitems = $catalogitems;

        return $this;
    }

    /**
     * Get catalogitems
     *
     * @return \Trazeo\BaseBundle\Entity\ECatalogItem
     */
    public function getECatalogitem()
    {
        return $this->catalogitems;
    }

    /**
     * Set catalogitems
     *
     * @param \Trazeo\BaseBundle\Entity\ECatalogItem $catalogitems
     *
     * @return File
     */
    public function setCatalogitem(\Trazeo\BaseBundle\Entity\ECatalogItem $catalogitems = null)
    {
        $this->catalogitems = $catalogitems;

        return $this;
    }

    /**
     * Get catalogitems
     *
     * @return \Trazeo\BaseBundle\Entity\ECatalogItem
     */
    public function getCatalogitems()
    {
        return $this->catalogitems;
    }

    /**
     * Set catalogitems
     *
     * @param \Trazeo\BaseBundle\Entity\ECatalogItem $catalogitems
     *
     * @return File
     */
    public function setCatalogitems(\Trazeo\BaseBundle\Entity\ECatalogItem $catalogitems = null)
    {
        $this->catalogitems = $catalogitems;

        return $this;
    }
}
