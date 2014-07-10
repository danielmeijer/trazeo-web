<?php
 namespace Sopinet\Bundle\GamificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
 /**
 * Entity UserAction
 *
 * @ORM\Table("e_useraction")
 * @ORM\Entity
 */
 class EUserAction
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
	 * @ORM\ManyToOne(targetEntity="Action")
	 * @ORM\JoinColumn(name="action_id", referencedColumnName="id", nullable=true)
	 */
	 protected $action;

	/**
	 * @ORM\ManyToOne(targetEntity="Sequence")
	 * @ORM\JoinColumn(name="sequence_id", referencedColumnName="id", nullable=true)
	 */
	 protected $sequence;

	/**
	 * @ORM\ManyToOne(targetEntity="\Sopinet\UserBundle\Entity\SopinetUserExtend")
	 */
	 protected $sopinetuserextends;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sopinetuserextends = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add sopinetuserextends
     *
     * @param \Sopinet\UserBundle\Entity\SopinetUserExtend $sopinetuserextends
     *
     * @return EUserAction
     */
    public function addSopinetuserextend(\Sopinet\UserBundle\Entity\SopinetUserExtend $sopinetuserextends)
    {
        $this->sopinetuserextends[] = $sopinetuserextends;

        return $this;
    }

    /**
     * Remove sopinetuserextends
     *
     * @param \Sopinet\UserBundle\Entity\SopinetUserExtend $sopinetuserextends
     */
    public function removeSopinetuserextend(\Sopinet\UserBundle\Entity\SopinetUserExtend $sopinetuserextends)
    {
        $this->sopinetuserextends->removeElement($sopinetuserextends);
    }

    /**
     * Get sopinetuserextends
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSopinetuserextends()
    {
        return $this->sopinetuserextends;
    }

    /**
     * Set sopinetuserextends
     *
     * @param \Sopinet\UserBundle\Entity\SopinetUserExtend $sopinetuserextends
     *
     * @return EUserAction
     */
    public function setSopinetuserextends(\Sopinet\UserBundle\Entity\SopinetUserExtend $sopinetuserextends = null)
    {
        $this->sopinetuserextends = $sopinetuserextends;

        return $this;
    }

    /**
     * Set action
     *
     * @param \Sopinet\Bundle\GamificationBundle\Entity\Action $action
     *
     * @return EUserAction
     */
    public function setAction(\Sopinet\Bundle\GamificationBundle\Entity\Action $action = null)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return \Sopinet\Bundle\GamificationBundle\Entity\Action
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set sequence
     *
     * @param \Sopinet\Bundle\GamificationBundle\Entity\Sequence $sequence
     *
     * @return EUserAction
     */
    public function setSequence(\Sopinet\Bundle\GamificationBundle\Entity\Sequence $sequence = null)
    {
        $this->sequence = $sequence;

        return $this;
    }

    /**
     * Get sequence
     *
     * @return \Sopinet\Bundle\GamificationBundle\Entity\Sequence
     */
    public function getSequence()
    {
        return $this->sequence;
    }
}
