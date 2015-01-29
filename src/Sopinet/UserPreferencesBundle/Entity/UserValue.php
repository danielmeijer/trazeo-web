<?php 
namespace Sopinet\UserPreferencesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Event\OnFlushEventArgs;
 
/**
 * @ORM\Entity(repositoryClass="Sopinet\UserPreferencesBundle\Entity\UserValueRepository")
 * @ORM\Table(name="user_value")
 * @DoctrineAssert\UniqueEntity("id")
 */
class UserValue
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\ManyToOne(targetEntity="Sopinet\UserBundle\Entity\SopinetUserExtend", inversedBy="uservalues")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     * @Exclude
     */
    protected $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="UserSetting", inversedBy="uservalues")
     * @ORM\JoinColumn(name="setting_id", referencedColumnName="id", nullable=true)
     */
    protected $setting;

    /**
     * @ORM\Column(name="value", type="string", length=255)
     */
    protected $value;    

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
     * Set value
     *
     * @param string $value
     * @return UserValue
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set user
     *
     * @param \Application\Sonata\UserBundle\Entity\User $user
     * @return UserValue
     */
    public function setUser(\Sopinet\UserBundle\Entity\SopinetUserExtend $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Sonata\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set setting
     *
     * @param UserSetting $setting
     * @return UserValue
     */
    public function setSetting(UserSetting $setting = null)
    {
        $this->setting = $setting;
    
        return $this;
    }

    /**
     * Get setting
     *
     * @return UserSetting 
     */
    public function getSetting()
    {
        return $this->setting;
    }
}