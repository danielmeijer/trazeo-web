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
    private $id;

    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\UserExtend", inversedBy="children")
     *  @ORM\JoinColumn(name="userExtend_children", referencedColumnName="id")
     */
    private $userExtend;

    /**
     * @var string
     *
     * @ORM\Column(name="nick", type="string", length=255)
     */
    private $nick;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateBirth", type="datetime")
     */
    private $dateBirth;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visibility", type="boolean")
     */
    private $visibility;

    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Groups", inversedBy="children")
     *  @ORM\JoinColumn(name="groups_children", referencedColumnName="id")
     */
    private $groups;

    /**
     * @var string
     *
     * @ORM\Column(name="sex", type="string", length=255)
     */
    private $sex;


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
     * Set userExtend
     *
     * @param string $userExtend
     *
     * @return Children
     */
    public function setUserExtend($userExtend)
    {
        $this->userExtend = $userExtend;

        return $this;
    }

    /**
     * Get userExtend
     *
     * @return string 
     */
    public function getUserExtend()
    {
        return $this->userExtend;
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
     * Set groups
     *
     * @param string $groups
     *
     * @return Children
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;

        return $this;
    }

    /**
     * Get groups
     *
     * @return string 
     */
    public function getGroups()
    {
        return $this->groups;
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
}

