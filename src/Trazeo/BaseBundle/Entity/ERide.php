<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\Exclude;

/**
 * Entity ERide
 *
 * @ORM\Table("e_ride")
 * @ORM\Entity(repositoryClass="ERideRepository")
 */
class ERide extends AbstractEntity
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
     * @Exclude
	 **/
	protected $events;

    /**
     * @ORM\OneToMany(targetEntity="EChild", mappedBy="ride")
     **/
    protected $childs;

    /**
     * @ORM\Column(name="fixChildCount", type="integer", length=2, nullable=true)
     */
    protected $fixChildCount;

	/**
	 * @ORM\OneToMany(targetEntity="EReport", mappedBy="ride", cascade={"remove"})
     * @Exclude
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
     * @ORM\ManyToOne(targetEntity="EGroup", inversedBy="ridesRegistered")
     * @ORM\JoinColumn(name="groupRegistered_id", referencedColumnName="id", nullable=true)
     * @Exclude
     **/
    protected $groupRegistered;

    /**
     * @ORM\ManyToOne(targetEntity="UserExtend")
     **/
    protected $userextend;

    protected $countReport;

    protected $childsR;

    protected $stringChildsR;

    protected $countChildsR;

    public function countReport() {
        return count($this->getReports());
    }

    public function getChildsR() {
        $repositoryRide = $this->getRepository();
        $this->childsR = $repositoryRide->getChildrenInRide($this);
        //ldd($this->childsR);
        return $this->childsR;
    }

    public function getStringChildsR() {
        $names = array();
        /** @var EChildRide $child */
        foreach($this->getChildsR() as $child) {
            $names[] = $child->getChild()->getNick();
        }
        $this->stringChildsR = implode(", ", $names);
        return $this->stringChildsR;
    }

    public function getCountChildsR() {
        if ($this->getFixChildCount() != null) $this->countChildsR = $this->getFixChildCount();
        else $this->countChildsR = count($this->getChildsR());
        return $this->countChildsR;
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

    public function getDurationSeconds() {
        $duration = $this->getDuration();
        if ($duration == null) return 0;
        $temp1 = explode(",", $duration);
        $temp2 = explode(" y ", $temp1[1]);

        $htemp = explode(" ", $temp1[0]);
        $mtemp = explode(" ", trim($temp2[0]));
        $stemp = explode(" ", trim($temp2[1]));

        $h = $htemp[0];
        $m = $mtemp[0];
        $s = $stemp[0];

        return ($h*60*60) + ($m*60) + $s;
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
        if ($this->groupid == null) {
            if ($this->getGroupRegistered() != null) $this->groupid = $this->getGroupRegistered()->getId();
        }
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

    /**
     * Add childs
     *
     * @param \Trazeo\BaseBundle\Entity\EChild $childs
     * @return ERide
     */
    public function addChild(\Trazeo\BaseBundle\Entity\EChild $childs)
    {
        $this->childs[] = $childs;

        return $this;
    }

    /**
     * Remove childs
     *
     * @param \Trazeo\BaseBundle\Entity\EChild $childs
     */
    public function removeChild(\Trazeo\BaseBundle\Entity\EChild $childs)
    {
        $this->childs->removeElement($childs);
    }

    /**
     * Get childs
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChilds()
    {
        return $this->childs;
    }

    /**
     * Set fixChildCount
     *
     * @param integer $fixChildCount
     * @return ERide
     */
    public function setFixChildCount($fixChildCount)
    {
        $this->fixChildCount = $fixChildCount;

        return $this;
    }

    /**
     * Get fixChildCount
     *
     * @return integer
     */
    public function getFixChildCount()
    {
        return $this->fixChildCount;
    }

    /**
     * Set groupRegistered
     *
     * @param \Trazeo\BaseBundle\Entity\EGroup $groupRegistered
     * @return ERide
     */
    public function setGroupRegistered(\Trazeo\BaseBundle\Entity\EGroup $groupRegistered = null)
    {
        $this->groupRegistered = $groupRegistered;
        $this->groupid = $groupRegistered->getId();

        return $this;
    }

    /**
     * Get groupRegistered
     *
     * @return \Trazeo\BaseBundle\Entity\EGroup 
     */
    public function getGroupRegistered()
    {
        return $this->groupRegistered;
    }
}
