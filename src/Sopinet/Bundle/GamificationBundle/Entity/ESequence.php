<?php
 namespace Sopinet\Bundle\GamificationBundle\Entity;

 use Doctrine\ORM\Mapping as ORM;
 use Knp\DoctrineBehaviors\Model as ORMBehaviors;

 /**
 * Entity ESequence
 *
 * @ORM\Table("e_sequence")
 * @ORM\Entity
 */
 class ESequence
 {

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	 protected $id;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="unique", type="boolean")
	 */
	 protected $unique=false;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="points", type="integer")
	 */
	 protected $points;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name", type="string")
	 */
	 protected $name;

	/**
	 * @ORM\ManyToMany(targetEntity="EAction")
	 */
	 protected $actions;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->actions = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set unique
     *
     * @param boolean $unique
     *
     * @return ESequence
     */
    public function setUnique($unique)
    {
        $this->unique = $unique;

        return $this;
    }

    /**
     * Get unique
     *
     * @return boolean
     */
    public function getUnique()
    {
        return $this->unique;
    }

    /**
     * Set points
     *
     * @param integer $points
     *
     * @return ESequence
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
     * Set name
     *
     * @param string $name
     *
     * @return ESequence
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

    /**
     * Add actions
     *
     * @param \Sopinet\Bundle\GamificationBundle\Entity\EAction $actions
     *
     * @return ESequence
     */
    public function addAction(\Sopinet\Bundle\GamificationBundle\Entity\EAction $actions)
    {
        $this->actions[] = $actions;

        return $this;
    }

    /**
     * Remove actions
     *
     * @param \Sopinet\Bundle\GamificationBundle\Entity\EAction $actions
     */
    public function removeAction(\Sopinet\Bundle\GamificationBundle\Entity\EAction $actions)
    {
        $this->actions->removeElement($actions);
    }

    /**
     * Get actions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getActions()
    {
        return $this->actions;
    }
}
