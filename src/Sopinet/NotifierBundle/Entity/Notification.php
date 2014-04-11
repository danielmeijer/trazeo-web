<?php 
namespace Sopinet\NotifierBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Event\OnFlushEventArgs;
 
/**
 * @ORM\Entity
 * @ORM\Table(name="notification")
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
     * @ORM\Column(name="object", type="integer", length=20)
     */
    protected $object;
    
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
     * @ORM\ManyToOne(targetEntity="Trazeo\BaseBundle\Entity\UserExtend", inversedBy="notifications")
     * @ORM\JoinColumn(name="userextend_notification", referencedColumnName="id", nullable=true, onDelete="CASCADE") 
     */
    protected $userextend;
    
    /**
     * @ORM\Column(name="date_register", type="datetime")
     * @var \Date $date_register
     */
    protected $date_register;

    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->date_register = new \DateTime();
    }    
    
}