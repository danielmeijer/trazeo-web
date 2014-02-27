<?php

namespace Trazeo\BaseBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class UserExtend
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;    

    public function __construct()
    {
        parent::__construct();
    }
    
    
    /**
     * @ORM\OneToOne(targetEntity="Application\Sonata\UserBundle\Entity\User", inversedBy="userextend")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;
    
    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Groups", inversedBy="userextend")
     */
    private $groups;
    
    /** @ORM\OneToMany(targetEntity="Trazeo\BaseBundle\Entity\Groups", mappedBy="admin")
     */
    private $admingroup;
    
    /** @ORM\OneToMany(targetEntity="Trazeo\BaseBundle\Entity\Route", mappedBy="admin")
     */
    private $adminroutes;
    
    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Children", mappedBy="userextend")
     */
    private $children;
    
    
    /** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\City", inversedBy="userextend")
     */
    private $city;
    
    /** @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\Country", inversedBy="userextend")
     */
    private $country;
    
    /**
     * @var string
     *
     * @ORM\Column(name="nick", type="string", length=255)
     */
    private $nick;

}