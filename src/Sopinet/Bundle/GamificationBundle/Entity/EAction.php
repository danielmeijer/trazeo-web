<?php
 namespace Sopinet\Bundle\GamificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
 /**
 * Entity Action
 *
 * @ORM\Table("e_action")
 * @ORM\Entity
 */
 class EAction
 {

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id",type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	 protected $id;

	/**
	 * @var boolean
	 *
	 * @ORM\Column(name="unique",type="boolean")
	 */
	 protected $unique=false;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="name",type="string")
	 */
	 protected $name;


	/**
	 * @var string
	 *
	 * @ORM\Column(name="time_restriction",type="string")
	 */
	 protected $time_restriction;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="points",type="integer")
	 */
	 protected $points;





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
     * @return EAction
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
     * Set time_restriction
     *
     * @param string $timeRestriction
     *
     * @return EAction
     */
    public function setTimeRestriction($timeRestriction)
    {
        $this->time_restriction = $timeRestriction;

        return $this;
    }

    /**
     * Get time_restriction
     *
     * @return string
     */
    public function getTimeRestriction()
    {
        return $this->time_restriction;
    }

    /**
     * Set points
     *
     * @param integer $points
     *
     * @return EAction
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
     * @return EAction
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
}
