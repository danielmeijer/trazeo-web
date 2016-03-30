<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\Exclude;
/**
 * Entity Route
 *
 * @ORM\Table("e_route")
 * @ORM\Entity
 */
class ERoute
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

    /** @ORM\OneToOne(targetEntity="EGroup", mappedBy="route")
     *  @Exclude
     */
    protected $group;

    /** @ORM\OneToMany(targetEntity="EPoints", mappedBy="route", cascade={"remove"})
     */
    protected $points;

    /** @ORM\ManyToOne(targetEntity="UserExtend", inversedBy="adminRoutes")
     *  @ORM\JoinColumn(name="admin_id", referencedColumnName="id")
     *  @Exclude
     */
    protected $admin;

    /** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\City")
     */
    protected $city;

    /** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\Country")
     */
    protected $country;

    /**
     * @var string
     *
     * @ORM\Column(name="distance", type="string", length=50, nullable=true)
     */
    protected $distance;

    /**
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="endAdress", type="string", length=255)
     */
    protected $endAdress;

    /**
     * @var string
     *
     * @ORM\Column(name="startAdress", type="string", length=255)
     */
    protected $startAdress;


    public function __toString() {

        return $this->getName();
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
     * @return ERoute
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
     * Set admin
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $admin
     *
     * @return ERoute
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
     * Set city
     *
     * @param \JJs\Bundle\GeonamesBundle\Entity\City $city
     *
     * @return ERoute
     */
    public function setCity(\JJs\Bundle\GeonamesBundle\Entity\City $city = null)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return \JJs\Bundle\GeonamesBundle\Entity\City
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set country
     *
     * @param \JJs\Bundle\GeonamesBundle\Entity\Country $country
     *
     * @return ERoute
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
        return $this->country;
    }

    /**
     * Add points
     *
     * @param \Trazeo\BaseBundle\Entity\EPoints $points
     *
     * @return ERoute
     */
    public function addPoint(\Trazeo\BaseBundle\Entity\EPoints $points)
    {
        $this->points[] = $points;

        return $this;
    }

    /**
     * Remove points
     *
     * @param \Trazeo\BaseBundle\Entity\EPoints $points
     */
    public function removePoint(\Trazeo\BaseBundle\Entity\EPoints $points)
    {
        $this->points->removeElement($points);
    }

    /**
     * Get points
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set ride
     *
     * @param \Trazeo\BaseBundle\Entity\ERide $ride
     *
     * @return ERoute
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
     * Set distance
     *
     * @param string $distance
     *
     * @return ERoute
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return string
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return ERoute
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->points = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set group
     *
     * @param \Trazeo\BaseBundle\Entity\EGroup $group
     *
     * @return ERoute
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
     * Set endAdress
     *
     * @param string $endAdress
     * @return ERoute
     */
    public function setEndAdress($endAdress)
    {
        $this->endAdress = $endAdress;

        return $this;
    }

    /**
     * Get endAdress
     *
     * @return string 
     */
    public function getEndAdress()
    {
        return $this->endAdress;
    }

    /**
     * Set startAdress
     *
     * @param string $startAdress
     * @return ERoute
     */
    public function setStartAdress($startAdress)
    {
        $this->startAdress = $startAdress;

        return $this;
    }

    /**
     * Get startAdress
     *
     * @return string 
     */
    public function getStartAdress()
    {
        return $this->startAdress;
    }
}
