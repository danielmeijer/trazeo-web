<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Entity ERide
 *
 * @ORM\Table("e_ride")
 * @ORM\Entity(repositoryClass="ERideRepository")
 */
class ERide
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
	 * @ORM\OneToOne(targetEntity="EGroup", inversedBy="ride")
	 * @ORM\JoinColumn(name="group_id", referencedColumnName="id", nullable=true)
	 **/
	protected $group;
	
	/**
	 * @ORM\OneToMany(targetEntity="EEvent", mappedBy="ride", cascade={"remove"})
	 **/
	protected $events;

	/**
	 * @ORM\OneToMany(targetEntity="EReport", mappedBy="ride", cascade={"remove"})
	 **/
	protected $reports;
	
	/**
	 * @ORM\Column(name="duration", type="string", length=255, nullable=true)
	 */
	protected $duration;
	
	/**
	 * @ORM\Column(name="distance", type="integer", length=255, nullable=true)
	 */
	protected $distance;
	
	/**
	 * @ORM\Column(name="groupid", type="string", length=50, nullable=true)
	 */
	protected $groupid;

    /**
     * @ORM\ManyToOne(targetEntity="UserExtend")
     **/
    protected $userextend;

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
     * Constructor
     */
    public function __construct()
    {
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set time_ini
     *
     * @param \DateTime $timeIni
     *
     * @return ERide
     */
    public function setTimeIni($timeIni)
    {
        $this->time_ini = $timeIni;

        return $this;
    }

    /**
     * Get time_ini
     *
     * @return \DateTime 
     */
    public function getTimeIni()
    {
        return $this->time_ini;
    }

    /**
     * Set time_fin
     *
     * @param \DateTime $timeFin
     *
     * @return ERide
     */
    public function setTimeFin($timeFin)
    {
        $this->time_fin = $timeFin;

        return $this;
    }

    /**
     * Get time_fin
     *
     * @return \DateTime 
     */
    public function getTimeFin()
    {
        return $this->time_fin;
    }

    /**
     * Set route
     *
     * @param \Trazeo\BaseBundle\Entity\ERoute $route
     *
     * @return ERide
     */
    public function setRoute(\Trazeo\BaseBundle\Entity\ERoute $route = null)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return \Trazeo\BaseBundle\Entity\ERoute 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Add events
     *
     * @param \Trazeo\BaseBundle\Entity\EEvent $events
     *
     * @return ERide
     */
    public function addEvent(\Trazeo\BaseBundle\Entity\EEvent $events)
    {
        $this->events[] = $events;

        return $this;
    }

    /**
     * Remove events
     *
     * @param \Trazeo\BaseBundle\Entity\EEvent $events
     */
    public function removeEvent(\Trazeo\BaseBundle\Entity\EEvent $events)
    {
        $this->events->removeElement($events);
    }

    /**
     * Get events
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * Set group
     *
     * @param \Trazeo\BaseBundle\Entity\EGroup $group
     *
     * @return ERide
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
     * Set go
     *
     * @param boolean $go
     *
     * @return ERide
     */
    public function setGo($go)
    {
        $this->go = $go;

        return $this;
    }

    /**
     * Get go
     *
     * @return boolean 
     */
    public function getGo()
    {
        return $this->go;
    }

    /**
     * Add reports
     *
     * @param \Trazeo\BaseBundle\Entity\EReport $reports
     *
     * @return ERide
     */
    public function addReport(\Trazeo\BaseBundle\Entity\EReport $reports)
    {
        $this->reports[] = $reports;

        return $this;
    }

    /**
     * Remove reports
     *
     * @param \Trazeo\BaseBundle\Entity\EReport $reports
     */
    public function removeReport(\Trazeo\BaseBundle\Entity\EReport $reports)
    {
        $this->reports->removeElement($reports);
    }

    /**
     * Get reports
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getReports()
    {
        return $this->reports;
    }

    /**
     * Set duration
     *
     * @param string $duration
     *
     * @return ERide
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return string 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set groupid
     *
     * @param string $groupid
     *
     * @return ERide
     */
    public function setGroupid($groupid)
    {
        $this->groupid = $groupid;

        return $this;
    }

    /**
     * Get groupid
     *
     * @return string 
     */
    public function getGroupid()
    {
        return $this->groupid;
    }
    
    public function __toString() {
    	return "Grupo " . $this->getGroupid() . " /Id " . $this->getId();
    }

    /**
     * Set distance
     *
     * @param integer $distance
     *
     * @return ERide
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return integer
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set userextend
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userextend
     *
     * @return ERide
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
}
