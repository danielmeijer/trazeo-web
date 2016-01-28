<?php 
namespace Sopinet\UserPreferencesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Event\OnFlushEventArgs;
 
/**
 * @ORM\Entity
 * @ORM\Table(name="user_setting")
 * @DoctrineAssert\UniqueEntity("id")
 */
class UserSetting
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @ORM\Column(name="type", type="string", length=255)
     * // Enum
     */    
    protected $type;
    
    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;
    
    /**
     * @ORM\Column(name="icon", type="string", length=255)
     */
    protected $icon;    

    /**
     * @ORM\Column(name="options", type="string", length=500)
     */
    protected $options;
    
    /**
     * @ORM\OneToMany(targetEntity="UserValue", mappedBy="setting")
     * @Exclude
     */
    protected $uservalues;    
    
    /**
     * @ORM\Column(name="defaultoption", type="string", length=255)
     */
    protected $defaultoption;    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->uservalues = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set type
     *
     * @param string $type
     * @return UserSetting
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return UserSetting
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
     * Set options
     *
     * @param string $options
     * @return UserSetting
     */
    public function setOptions($options)
    {
        $this->options = $options;
    
        return $this;
    }

    /**
     * Get options
     *
     * @return string 
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Add uservalues
     *
     * @param UserValue $uservalues
     * @return UserSetting
     */
    public function addUservalue(UserValue $uservalues)
    {
        $this->uservalues[] = $uservalues;
    
        return $this;
    }

    /**
     * Remove uservalues
     *
     * @param UserValue $uservalues
     */
    public function removeUservalue(UserValue $uservalues)
    {
        $this->uservalues->removeElement($uservalues);
    }

    /**
     * Get uservalues
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUservalues()
    {
        return $this->uservalues;
    }

    /**
     * Set icon
     *
     * @param string $icon
     * @return UserSetting
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    
        return $this;
    }

    /**
     * Get icon
     *
     * @return string 
     */
    public function getIcon()
    {
        return $this->icon;
    }
    
    public function getOptionsArr() {
    	return explode(",", $this->getOptions());
    }
    
    public function getOptionsCont() {
    	return count($this->getOptionsArr());
    }
    
    public function getDescription()
    {
    	return $this->getName() . ".description";
    }

    /**
     * Set defaultoption
     *
     * @param string $defaultoption
     * @return UserSetting
     */
    public function setDefaultoption($defaultoption)
    {
        $this->defaultoption = $defaultoption;
    
        return $this;
    }

    /**
     * Get defaultoption
     *
     * @return string 
     */
    public function getDefaultoption()
    {
        return $this->defaultoption;
    }
}