<?php 
namespace Sopinet\UserNotificationsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Event\OnFlushEventArgs;
 
/**
 * @ORM\Entity
 * @ORM\Table(name="user_notification")
 * @DoctrineAssert\UniqueEntity("id")
 */
class Notification
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="action", type="string", length=100)
     */
    protected $action;

    /**
     * @ORM\Column(name="object", type="string", length=100)
     */
    protected $object;
    
    /**
     * @ORM\Column(name="object_id", type="integer", length=20, nullable=true)
     */
    protected $object_id;
    
    /**
     * @ORM\Column(name="view", type="boolean")
     */
    protected $view;
    
    /**
     * @ORM\Column(name="view_complete", type="boolean", nullable=true)
     */
    protected $view_complete;

    /**
     * @ORM\Column(name="email", type="boolean")
     */
    protected $email;
    
    /**
     * @ORM\ManyToOne(targetEntity="\Sopinet\UserBundle\Entity\SopinetUserExtend", inversedBy="notifications")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true, onDelete="CASCADE") 
     */
    protected $user; 
    
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
     * Set action
     *
     * @param string $action
     * @return Notification
     */
    public function setAction($action)
    {
        $this->action = $action;
    
        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set object
     *
     * @param integer $object
     * @return Notification
     */
    public function setObject($object)
    {
        $this->object = $object;
    
        return $this;
    }

    /**
     * Get object
     *
     * @return integer 
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set view
     *
     * @param boolean $view
     * @return Notification
     */
    public function setView($view)
    {
        $this->view = $view;
    
        return $this;
    }

    /**
     * Get view
     *
     * @return boolean 
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Set email
     *
     * @param boolean $email
     * @return Notification
     */
    public function setEmail($email)
    {
        $this->email = $email;
    
        return $this;
    }

    /**
     * Get email
     *
     * @return boolean 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set user
     *
     * @param \Sopinet\UserBundle\Entity\SopinetUserExtend $user
     * @return Notification
     */
    public function setUser(\Sopinet\UserBundle\Entity\SopinetUserExtend $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Sopinet\UserBundle\Entity\SopinetUserExtend 
     */
    public function getUser()
    {
        return $this->user;
    }
}