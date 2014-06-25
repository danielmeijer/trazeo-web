<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Entity GroupAnonInvite
 *
 * @ORM\Entity(repositoryClass="EGroupAnonInviteRepository")
 * @ORM\Table(name="e_group_anoninvite")
 * @DoctrineAssert\UniqueEntity("id")
 */
class EGroupAnonInvite
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
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    protected $email;
    
    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    protected $token;    
    
    /** @ORM\ManyToOne(targetEntity="EGroup", inversedBy="inviteGroupAnon")
     *  @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    protected $group;
    
    /** @ORM\ManyToOne(targetEntity="UserExtend", inversedBy="inviteAnonGroup")
     *  @ORM\JoinColumn(name="userextend_id", referencedColumnName="id")
     */
    protected $userCreated;
    
    private function rand_string( $length ) {
    	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    	$str = "";
    	$size = strlen( $chars );
    	for( $i = 0; $i < $length; $i++ ) {
    		$str .= $chars[ rand( 0, $size - 1 ) ];
    	}
    	return $str;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->token = $this->rand_string(255);
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
     * Set email
     *
     * @param string $email
     *
     * @return EGroupAnonInvite
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return EGroupAnonInvite
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set group
     *
     * @param \Trazeo\BaseBundle\Entity\EGroup $group
     *
     * @return EGroupAnonInvite
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
     * Set userCreated
     *
     * @param \Trazeo\BaseBundle\Entity\UserExtend $userCreated
     *
     * @return EGroupAnonInvite
     */
    public function setUserCreated(\Trazeo\BaseBundle\Entity\UserExtend $userCreated = null)
    {
        $this->userCreated = $userCreated;

        return $this;
    }

    /**
     * Get userCreated
     *
     * @return \Trazeo\BaseBundle\Entity\UserExtend
     */
    public function getUserCreated()
    {
        return $this->userCreated;
    }
}
