<?php 
namespace Sopinet\Bundle\UserNotificationsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
 
/**
 * @ORM\Entity(repositoryClass="Sopinet\Bundle\UserNotificationsBundle\Entity\UserLiveRepository")
 * @ORM\Table(name="user_live")
 * @DoctrineAssert\UniqueEntity("id")
 */
class UserLive
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Sopinet\UserBundle\Entity\SopinetUserExtend", inversedBy="userlives")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     */
    protected $user;

    /**
     * @ORM\Column(name="value", type="string", length=255, nullable=true)
     */
    protected $value=null;

   /**
    * Obtiene el valor de un usuario para una configuración
    * Si existe la configuración la devuelve, sino, retorna el valor por defecto.
    * 
    * @return string Value
    */
    public function getValue() {
      return explode(',', $this->value);
    }  
   /**
    * Establece el valor para la configuración del usuario
    * 
    * @param string value
    */
    public function setValue($value) {
            $this->value=$value;
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
     * Set user
     *
     * @param \Sopinet\UserBundle\Entity\SopinetUserExtend $user
     *
     * @return UserLive
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
