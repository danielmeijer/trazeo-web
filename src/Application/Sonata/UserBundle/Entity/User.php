<?php

namespace Application\Sonata\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;

use Doctrine\ORM\Mapping as ORM;
/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
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
     * @ORM\OneToOne(targetEntity="Trazeo\BaseBundle\Entity\UserExtend", mappedBy="user")
     * @ORM\JoinColumn(name="userextend_id", referencedColumnName="id")
     */
    private $userextend;
    
    /**
     * Set address
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userextend
     * @return Order
     */
    public function setUserExtend(\Trazeo\BaseBundle\Entity\UserExtend $userextend = null)
    {
    	$this->userextend = $userextend;
    
    	return $this;
    }
    
    /**
     * Get address
     *
     * @return \Trazeo\BaseBundle\Entity\UserExtend
     */
    public function getUserExtend()
    {
    	return $this->userextend;
    }    
}