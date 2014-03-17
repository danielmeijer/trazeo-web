<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Children
 *
 * @ORM\Table("children")
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
    
    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\UserExtend", mappedBy="children")
     * @ORM\JoinColumn(name="children_userextend", referencedColumnName="id")
     */
    protected $userextendchildren;
    
    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Groups", mappedBy="children")
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
	 * @ORM\Column(name="sex", type="string")
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
    
    public function __toString(){
    	if($this->nick == "")
    		return (string)$this->id;
    	return $this->nick;
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
     *
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
     *
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
     *
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
     *
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
     * Add userextendchildren
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userextendchildren
     *
     * @return Children
     */
    public function addUserextendchild(\Trazeo\BaseBundle\Entity\UserExtend $userextendchildren)
    {
        $this->userextendchildren[] = $userextendchildren;

        return $this;
    }

    /**
     * Remove userextendchildren
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userextendchildren
     */
    public function removeUserextendchild(\Trazeo\BaseBundle\Entity\UserExtend $userextendchildren)
    {
        $this->userextendchildren->removeElement($userextendchildren);
    }

    /**
     * Get userextendchildren
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserextendchildren()
    {
        return $this->userextendchildren;
    }

    /**
     * Add groups
     *
     * @param \Trazeo\BaseBundle\Entity\Groups $groups
     *
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
