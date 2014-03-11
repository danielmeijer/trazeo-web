<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Children
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Children
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\UserExtend", inversedBy="children")
     *  @ORM\JoinColumn(name="userExtend_children", referencedColumnName="id")
     */
    protected $userExtendChildren;
    
    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Groups", mappedBy="children")
     *  @ORM\JoinColumn(name="groups_children", referencedColumnName="id")
     */
    protected $groups;

    /**
     * @var string
     *
     * @ORM\Column(name="nick", type="string", length=255)
     */
    protected $nick;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateBirth", type="datetime")
     */
    protected $dateBirth;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visibility", type="boolean")
     */
    protected $visibility;


	/**
	 * @ORM\Column(name="sex", type="string", columnDefinition="ENUM('H','M')")
	 */
	protected $sex;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userExtendChildren = new \Doctrine\Common\Collections\ArrayCollection();
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nick
     *
     * @param string $nick
     * @return Children
     */
    public function setNick($nick)
    {
        $this->nick = $nick;

        return $this;
    }

    /**
     * Get nick
     *
     * @return string 
     */
    public function getNick()
    {
        return $this->nick;
    }

    /**
     * Set dateBirth
     *
     * @param \DateTime $dateBirth
     * @return Children
     */
    public function setDateBirth($dateBirth)
    {
        $this->dateBirth = $dateBirth;

        return $this;
    }

    /**
     * Get dateBirth
     *
     * @return \DateTime 
     */
    public function getDateBirth()
    {
        return $this->dateBirth;
    }

    /**
     * Set visibility
     *
     * @param boolean $visibility
     * @return Children
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Get visibility
     *
     * @return boolean 
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set sex
     *
     * @param string $sex
     * @return Children
     */
    public function setSex($sex)
    {
        $this->sex = $sex;

        return $this;
    }

    /**
     * Get sex
     *
     * @return string 
     */
    public function getSex()
    {
        return $this->sex;
    }

    /**
     * Add userExtendChildren
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userExtendChildren
     * @return Children
     */
    public function addUserExtendChild(\Trazeo\BaseBundle\Entity\UserExtend $userExtendChildren)
    {
        $this->userExtendChildren[] = $userExtendChildren;

        return $this;
    }

    /**
     * Remove userExtendChildren
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userExtendChildren
     */
    public function removeUserExtendChild(\Trazeo\BaseBundle\Entity\UserExtend $userExtendChildren)
    {
        $this->userExtendChildren->removeElement($userExtendChildren);
    }

    /**
     * Get userExtendChildren
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserExtendChildren()
    {
        return $this->userExtendChildren;
    }

    /**
     * Add groups
     *
     * @param \Trazeo\BaseBundle\Entity\Groups $groups
     * @return Children
     */
    public function addGroup(\Trazeo\BaseBundle\Entity\Groups $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Trazeo\BaseBundle\Entity\Groups $groups
     */
    public function removeGroup(\Trazeo\BaseBundle\Entity\Groups $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
