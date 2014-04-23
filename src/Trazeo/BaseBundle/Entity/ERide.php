<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Entity ERide
 *
 * @ORM\Table("e_ride")
 * @ORM\Entity
 */
class ERide
{
	use \Sopinet\Bundle\SimplePointBundle\Model\GeoLocation;
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
	 * @ORM\Column(name="time_ini", type="datetime", nullable=true)
	 */
	protected $time_ini;
	
	/**
	 * @ORM\Column(name="time_fin", type="datetime", nullable=true)
	 */
	protected $time_fin;
	
	/**
	 * @ORM\OneToOne(targetEntity="EGroup", inversedBy="ride")
	 * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
	 **/
	protected $group;
	
	/**
	 * @ORM\OneToMany(targetEntity="EEvent", mappedBy="ride")
	 **/
	protected $events;
	
	/**
	 * @ORM\Column(name="go", type="boolean", nullable=true)
	 */
	protected $go;

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
}
