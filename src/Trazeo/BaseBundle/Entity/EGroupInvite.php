<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
/**
 * Entity EGroupInvite
 *
 * @ORM\Table("e_groupinvite")
 * @ORM\Entity
 */
class EGroupInvite
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

    /** @ORM\ManyToOne(targetEntity="UserExtend", inversedBy="inviteGroup")
     *  @ORM\JoinColumn(name="userextend_id", referencedColumnName="id")
     * @var unknown
     */
    protected $userextend;
    
    /** 
     * @ORM\ManyToOne(targetEntity="UserExtend", inversedBy="inviteGroupSender")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * @var unknown
     */
    protected $sender;


    /** @ORM\ManyToOne(targetEntity="EGroup", inversedBy="inviteGroup")
     *  @ORM\JoinColumn(name="group_id", referencedColumnName="id") 
     */
    protected $group;
    
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
     * Set userextend
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userextend
     *
     * @return EGroupInvite
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
     * Set group
     *
     * @param \Trazeo\BaseBundle\Entity\EGroup $group
     *
     * @return EGroupInvite
     */
    public function setGroup(\Trazeo\BaseBundle\Entity\EGroup $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \Trazeo\BaseBundle\Entity\EGroup 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set sender
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $sender
     *
     * @return EGroupInvite
     */
    public function setSender(\Trazeo\BaseBundle\Entity\UserExtend $sender = null)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * Get sender
     *
     * @return \Trazeo\BaseBundle\Entity\UserExtend
     */
    public function getSender()
    {
        return $this->sender;
    }
}
