<?php
 namespace Sopinet\Bundle\GamificationBundle\Entity;

 use Doctrine\ORM\Mapping as ORM;
 use Knp\DoctrineBehaviors\Model as ORMBehaviors;

 /**
 * Entity EUserAction
 *
 * @ORM\Table("e_useraction")
 * @ORM\Entity
 */
 class EUserAction
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
	 * @ORM\Column(name="entities_involved", type="string")
	 */
	 protected $entities_involved;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="ids", type="string")
	 */
	 protected $ids;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="acumulated", type="integer", nullable=true)
	 */
	 protected $acumulated=1;

	/**
	 * @ORM\ManyToOne(targetEntity="EAction")
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id", nullable=true)
	 */
	 protected $actions;

	/**
	 * @ORM\ManyToOne(targetEntity="\Sopinet\UserBundle\Entity\SopinetUserExtend")
     * @ORM\JoinColumn(name="sopinetuserextend_id", referencedColumnName="id", nullable=true)
	 */
	 protected $sopinetuserextends;

	/**
	 * @ORM\ManyToOne(targetEntity="ESequence")
	 */
	 protected $sequences;

    /**
     * @var boolean
     *
     * @ORM\Column(name="updated", type="boolean", nullable=true)
     */
     protected $updated=false;

    /**
     * @var integer
     *
     * @ORM\Column(name="lastupdate", type="integer", nullable=true)
     */
     protected $lastupdate=0;

    /**
     * Set entities_involved
     *
     * @param string $entitiesInvolved
     *
     * @return EUserAction
     */
    public function setEntitiesInvolved($entitiesInvolved)
    {
        $this->entities_involved = $entitiesInvolved;

        return $this;
    }

    /**
     * Get entities_involved
     *
     * @return string
     */
    public function getEntitiesInvolved()
    {
        return $this->entities_involved;
    }

    /**
     * Set ids
     *
     * @param string $ids
     *
     * @return EUserAction
     */
    public function setIds($ids)
    {
        $this->ids = $ids;

        return $this;
    }

    /**
     * Get ids
     *
     * @return string
     */
    public function getIds()
    {
        return $this->ids;
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
     * Set acumulated
     *
     * @param integer $acumulated
     *
     * @return EUserAction
     */
    public function setAcumulated($acumulated)
    {
        $this->acumulated = $acumulated;

        return $this;
    }

    /**
     * Get acumulated
     *
     * @return integer
     */
    public function getAcumulated()
    {
        return $this->acumulated;
    }

    /**
     * Set actions
     *
     * @param \Sopinet\Bundle\GamificationBundle\Entity\EAction $actions
     *
     * @return EUserAction
     */
    public function setActions(\Sopinet\Bundle\GamificationBundle\Entity\EAction $actions = null)
    {
        $this->actions = $actions;

        return $this;
    }

    /**
     * Get actions
     *
     * @return \Sopinet\Bundle\GamificationBundle\Entity\EAction
     */
    public function getActions()
    {
        return $this->actions;
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
     * Get sopinetuserextends
     *
     * @return \Sopinet\UserBundle\Entity\SopinetUserExtend
     */
    public function getSopinetuserextends()
    {
        return $this->sopinetuserextends;
    }

    /**
     * Set sequences
     *
     * @param \Sopinet\Bundle\GamificationBundle\Entity\ESequence $sequences
     *
     * @return EUserAction
     */
    public function setSequences(\Sopinet\Bundle\GamificationBundle\Entity\ESequence $sequences = null)
    {
        $this->sequences = $sequences;

        return $this;
    }

    /**
     * Get sequences
     *
     * @return \Sopinet\Bundle\GamificationBundle\Entity\ESequence
     */
    public function getSequences()
    {
        return $this->sequences;
    }

    /**
     * Set updated.
     *
     * @param boolean $updated
     *
     * @return EUserAction
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated.
     *
     * @return boolean
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Set lastupdate.
     *
     * @param integer $lastupdate
     *
     * @return EUserAction
     */
    public function setLastupdate($lastupdate)
    {
        $this->lastupdate = $lastupdate;

        return $this;
    }

    /**
     * Get lastupdate.
     *
     * @return integer
     */
    public function getLastupdate()
    {
        return $this->lastupdate;
    }
}
