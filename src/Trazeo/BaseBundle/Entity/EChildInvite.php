<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity EChildInvite
 *
 * @ORM\Table("e_childinvite")
 * @ORM\Entity
 */
class EChildInvite
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
     * @ORM\ManyToOne(targetEntity="UserExtend", inversedBy="inviteChild")
     * @ORM\JoinColumn(name="userextend_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $userextend;
    
    /** 
     * @ORM\ManyToOne(targetEntity="EChild", inversedBy="inviteChild")
     * @ORM\JoinColumn(name="child_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $child;
    
    /** 
     * @ORM\ManyToOne(targetEntity="UserExtend", inversedBy="inviteChildSender")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * @var unknown
     */
    protected $sender;
    

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
     * @return EChildInvite
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
     * Set child
     *
     * @param \Trazeo\BaseBundle\Entity\EChild $child
     *
     * @return EChildInvite
     */
    public function setChild(\Trazeo\BaseBundle\Entity\EChild $child = null)
    {
        $this->child = $child;

        return $this;
    }

    /**
     * Get child
     *
     * @return \Trazeo\BaseBundle\Entity\EChild 
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Set sender
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $sender
     *
     * @return EChildInvite
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
