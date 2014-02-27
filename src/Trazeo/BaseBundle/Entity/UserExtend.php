<?php

namespace Trazeo\BaseBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class UserExtend extends BaseUser
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
     * @var string
     *
     * @ORM\Column(name="nick", type="string", length=255)
     */
    private $nick;
    
    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Children", mappedBy="userExtend")
     *  @ORM\JoinColumn(name="children_userExtend", referencedColumnName="id")
     */
    private $children;
    
    /** @ORM\ManyToMany(targetEntity="Trazeo\BaseBundle\Entity\Groups", mappedBy="userExtend")
     */
    protected $groups;
    
    /** @ORM\OneToMany(targetEntity="Trazeo\BaseBundle\Entity\Groups", mappedBy="userExtend") */
    private $adminGroups;
    
    /** @ORM\OneToMany(targetEntity="Trazeo\BaseBundle\Entity\Route", mappedBy="userExtend") */
    private $adminRoutes;
    
    
    

    /** @ORM\OneToMany(targetEntity="Trazeo\BaseBundle\Entity\Route", mappedBy="admin") */
    private $route;
    
    
    
    
    
}