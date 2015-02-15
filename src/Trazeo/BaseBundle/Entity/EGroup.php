<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\Exclude;
/**
 * Entity Group
 *
 * @ORM\Table(name="e_group")
 * @ORM\Entity(repositoryClass="EGroupRepository")
 */
class EGroup
{
    const BYMODE_PEDIBUS = "pedibus";
    const BYMODE_BICIBUS = "bicibus";

    const VISIBILITY_PUBLIC = "0";
    const VISIBILITY_PRIVATE = "1";
    const VISIBILITY_HIDE = "2";

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
     * @ORM\ManyToMany(targetEntity="UserExtend", inversedBy="groups")
     * @ORM\JoinTable(name="groups_userextend")
     * @Exclude
     **/
    protected $userextendgroups;
    
    /** @ORM\ManyToOne(targetEntity="UserExtend", inversedBy="adminGroups")
     *  @Exclude
     */
    protected $admin;

    /**
     * @ORM\ManyToMany(targetEntity="UserExtend", inversedBy="mgroups")
     * @ORM\JoinTable(name="mgroups_userextend")
     * @Exclude
     **/
    protected $monitor_userextendgroups;
    
    /**
     * @ORM\ManyToMany(targetEntity="EChild", inversedBy="groups")
     * @ORM\JoinTable(name="groups_childs")
     **/
    protected $childs;

    /** 
     * @ORM\OneToOne(targetEntity="ERoute", inversedBy="group")
     * @ORM\JoinColumn(name="route_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $route;

    /**
     * @ORM\ManyToOne(targetEntity="\Trazeo\MyPageBundle\Entity\Page", inversedBy="groups")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $page;
    
    /**
     *
     * @ORM\Column(name="visibility", type="string", length=255, nullable=true)
     */
    protected $visibility;
    
    
    /** @ORM\OneToMany(targetEntity="EGroupAccess",  mappedBy="group", cascade={"remove"})
     * @var unknown
     */
    protected $access;
    
    /** @ORM\OneToMany(targetEntity="EGroupInvite",  mappedBy="group", cascade={"remove"})
     *  @Exclude
     */
    protected $inviteGroup;
    
    /** @ORM\OneToMany(targetEntity="EGroupAnonInvite",  mappedBy="group", cascade={"remove"})
     * @var unknown
     * @Exclude
     */
    protected $inviteGroupAnon;    
    
    /** 
     * @ORM\OneToOne(targetEntity="ERide", mappedBy="group", cascade={"remove"})
     */
    protected $ride;
    
    /**
     * @ORM\Column(name="hasRide", type="boolean", nullable=true)
     */
    protected $hasRide;

    /** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\City")
     */
    protected $city;
    
    /** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\Country")
     */
    protected $country;

    /**
     *
     * @ORM\Column(name="bymode", type="string", length=255, nullable=true)
     */
    protected $bymode;

    /**
     * @ORM\Column(name="privateMonitor", type="boolean", nullable=true)
     */
    protected $privateMonitor;

    /**
     * @var string
     *
     * @ORM\Column(name="school1", type="string", nullable=true)
     *
     */
    protected $school1;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    protected $numberChilds;

    protected $numberUsers;

    public function getNumberChilds() {
        return count($this->childs);
    }

    public function getNumberUsers() {
        return count($this->userextendgroups);
    }

    public function __toString(){
 
    	return $this->getName();
    }

    public function isMonitor(UserExtend $userextend) {
        if ($this->getPrivateMonitor() == null || $this->getPrivateMonitor() == false) {
            return true;
        }
        foreach($this->monitor_userextendgroups as $ue) {
            if ($userextend->getId() == $ue->getId()) return true;
        }
        return false;
    }
  
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->userextendgroups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->childs = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return EGroup
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add userextendgroups
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userextendgroups
     *
     * @return EGroup
     */
    public function addUserextendgroup(\Trazeo\BaseBundle\Entity\UserExtend $userextendgroups)
    {
        $this->userextendgroups[] = $userextendgroups;

        return $this;
    }

    /**
     * Remove userextendgroups
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userextendgroups
     */
    public function removeUserextendgroup(\Trazeo\BaseBundle\Entity\UserExtend $userextendgroups)
    {
        $this->userextendgroups->removeElement($userextendgroups);
    }

    /**
     * Get userextendgroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserextendgroups()
    {
        return $this->userextendgroups;
    }

    /**
     * Set admin
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $admin
     *
     * @return EGroup
     */
    public function setAdmin(\Trazeo\BaseBundle\Entity\UserExtend $admin = null)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return \Trazeo\BaseBundle\Entity\UserExtend 
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Add childs
     *
     * @param \Trazeo\BaseBundle\Entity\EChild $childs
     *
     * @return EGroup
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
     * Set route
     *
     * @param \Trazeo\BaseBundle\Entity\ERoute $route
     *
     * @return EGroup
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
     * Set visibility
     *
     * @param boolean $visibility
     *
     * @return EGroup
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
     * Add access
     *
     * @param \Trazeo\BaseBundle\Entity\EGroupAccess $access
     *
     * @return EGroup
     */
    public function addAccess(\Trazeo\BaseBundle\Entity\EGroupAccess $access)
    {
        $this->access[] = $access;

        return $this;
    }

    /**
     * Remove access
     *
     * @param \Trazeo\BaseBundle\Entity\EGroupAccess $access
     */
    public function removeAccess(\Trazeo\BaseBundle\Entity\EGroupAccess $access)
    {
        $this->access->removeElement($access);
    }

    /**
     * Get access
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Add invite
     *
     * @param \Trazeo\BaseBundle\Entity\EGroupInvite $invite
     *
     * @return EGroup
     */
    public function addInvite(\Trazeo\BaseBundle\Entity\EGroupInvite $invite)
    {
        $this->invite[] = $invite;

        return $this;
    }

    /**
     * Remove invite
     *
     * @param \Trazeo\BaseBundle\Entity\EGroupInvite $invite
     */
    public function removeInvite(\Trazeo\BaseBundle\Entity\EGroupInvite $invite)
    {
        $this->invite->removeElement($invite);
    }

    /**
     * Get invite
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInvite()
    {
        return $this->invite;
    }

    /**
     * Set hasRide
     *
     * @param boolean $hasRide
     *
     * @return EGroup
     */
    public function setHasRide($hasRide)
    {
        $this->hasRide = $hasRide;

        return $this;
    }

    /**
     * Get hasRide
     *
     * @return boolean 
     */
    public function getHasRide()
    {
        return $this->hasRide;
    }

    /**
     * Set ride
     *
     * @param \Trazeo\BaseBundle\Entity\ERide $ride
     *
     * @return EGroup
     */
    public function setRide(\Trazeo\BaseBundle\Entity\ERide $ride = null)
    {
        $this->ride = $ride;

        return $this;
    }

    /**
     * Get ride
     *
     * @return \Trazeo\BaseBundle\Entity\ERide 
     */
    public function getRide()
    {
        return $this->ride;
    }

    /**
     * Add inviteGroup
     *
     * @param \Trazeo\BaseBundle\Entity\EGroupInvite $inviteGroup
     *
     * @return EGroup
     */
    public function addInviteGroup(\Trazeo\BaseBundle\Entity\EGroupInvite $inviteGroup)
    {
        $this->inviteGroup[] = $inviteGroup;

        return $this;
    }

    /**
     * Remove inviteGroup
     *
     * @param \Trazeo\BaseBundle\Entity\EGroupInvite $inviteGroup
     */
    public function removeInviteGroup(\Trazeo\BaseBundle\Entity\EGroupInvite $inviteGroup)
    {
        $this->inviteGroup->removeElement($inviteGroup);
    }

    /**
     * Get inviteGroup
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInviteGroup()
    {
        return $this->inviteGroup;
    }

    /**
     * Add inviteGroupAnon
     *
     * @param \Trazeo\BaseBundle\Entity\EGroupInvite $inviteGroupAnon
     *
     * @return EGroup
     */
    public function addInviteGroupAnon(\Trazeo\BaseBundle\Entity\EGroupInvite $inviteGroupAnon)
    {
        $this->inviteGroupAnon[] = $inviteGroupAnon;

        return $this;
    }

    /**
     * Remove inviteGroupAnon
     *
     * @param \Trazeo\BaseBundle\Entity\EGroupInvite $inviteGroupAnon
     */
    public function removeInviteGroupAnon(\Trazeo\BaseBundle\Entity\EGroupInvite $inviteGroupAnon)
    {
        $this->inviteGroupAnon->removeElement($inviteGroupAnon);
    }

    /**
     * Get inviteGroupAnon
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getInviteGroupAnon()
    {
        return $this->inviteGroupAnon;
    }
    
    public function getCity()
    {

        if ($this->city!=null) {
            return $this->city;
        }
        elseif ($this->route != null) {
            if ($this->route->getCity() != null) {
                return $this->getRoute()->getCity();
            }
        }
    	return null;
    }

    /**
     * Set page
     *
     * @param \Trazeo\MyPageBundle\Entity\Page $page
     *
     * @return EGroup
     */
    public function setPage(\Trazeo\MyPageBundle\Entity\Page $page = null)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Set city
     *
     * @param \JJs\Bundle\GeonamesBundle\Entity\City $city
     *
     * @return EGroup
     */
    public function setCity(\JJs\Bundle\GeonamesBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get page
     *
     * @return \Trazeo\MyPageBundle\Entity\Page
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set country
     *
     * @param \JJs\Bundle\GeonamesBundle\Entity\Country $country
     *
     * @return EGroup
     */
    public function setCountry(\JJs\Bundle\GeonamesBundle\Entity\Country $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \JJs\Bundle\GeonamesBundle\Entity\Country
     */
    public function getCountry()
    {
        if ($this->getRoute() != null) {
            if ($this->getRoute()->getCountry() != null) {
                return $this->getRoute()->getCountry();
            }
        }
        elseif ($this->country!=null) {
            return $this->country;
        }
        return null;
    }

    /**
     * Set school1
     *
     * @param string $school1
     *
     * @return EGroup
     */
    public function setSchool1($school1)
    {
        $this->school1 = $school1;

        return $this;
    }

    /**
     * Get school1
     *
     * @return string
     */
    public function getSchool1()
    {
        return $this->school1;
    }

    /**
     * Set bymode
     *
     * @param string $bymode
     *
     * @return EGroup
     */
    public function setByMode($bymode)
    {
        $this->bymode = $bymode;

        return $this;
    }

    /**
     * Get bymode
     *
     * @return string
     */
    public function getByMode()
    {
        if ($this->bymode == "") return EGroup::BYMODE_PEDIBUS;
        return $this->bymode;
    }

    /**
     * Add monitor_userextendgroups
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $monitorUserextendgroups
     * @return EGroup
     */
    public function addMonitorUserextendgroup(\Trazeo\BaseBundle\Entity\UserExtend $monitorUserextendgroups)
    {
        $this->monitor_userextendgroups[] = $monitorUserextendgroups;

        return $this;
    }

    /**
     * Remove monitor_userextendgroups
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $monitorUserextendgroups
     */
    public function removeMonitorUserextendgroup(\Trazeo\BaseBundle\Entity\UserExtend $monitorUserextendgroups)
    {
        $this->monitor_userextendgroups->removeElement($monitorUserextendgroups);
    }

    /**
     * Get monitor_userextendgroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMonitorUserextendgroups()
    {
        return $this->monitor_userextendgroups;
    }

    /**
     * Set privateMonitor
     *
     * @param boolean $privateMonitor
     * @return EGroup
     */
    public function setPrivateMonitor($privateMonitor)
    {
        $this->privateMonitor = $privateMonitor;

        return $this;
    }

    /**
     * Get privateMonitor
     *
     * @return boolean 
     */
    public function getPrivateMonitor()
    {
        return $this->privateMonitor;
    }
}
