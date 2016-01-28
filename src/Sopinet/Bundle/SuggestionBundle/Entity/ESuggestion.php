<?php

namespace Sopinet\Bundle\SuggestionBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Event\OnFlushEventArgs;
 
/**
 * @ORM\Entity(repositoryClass="Sopinet\Bundle\SuggestionBundle\Entity\ESuggestionRepository")
 * @ORM\Table(name="e_suggestion")
 * @DoctrineAssert\UniqueEntity("id")
 */
class ESuggestion
{
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
	 * @ORM\Column(name="rule", type="string", length=255)
	 */
	protected $rule;
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="text", type="string", length=255)
	 */
	protected $text;

    
    /**
     * @var panel
     *
     * @ORM\Column(name="panel", type="string", length=255)
     */
    protected $panel;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="element", type="string", length=255)
	 */
	protected $element;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="forder", type="integer", length=10)
	 */
	protected $forder;
	
    /**
     * @ORM\Column(name="role", type="string", length=50, nullable=true)
     */    
    protected $role;
    
    /**
     * // Datos de posición: 'n','s','e','w'
     * @ORM\Column(name="position", type="string", length=1, nullable=true)
     */
    protected $position;

    
    /**
     * // Tiempo durante el que se muestra la sugerencía en ms (-1 para mostrar hasta que el usuario la cierre)
     * @ORM\Column(name="delay", type="integer", nullable=true)
     */
    protected $delay;

    /**
     * @ORM\Column(name="style", type="string", length=50, nullable=true)
     */
    protected $style;

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
     * Set rule
     *
     * @param string $rule
     *
     * @return ESuggestion
     */
    public function setRule($rule)
    {
        $this->rule = $rule;

        return $this;
    }

    /**
     * Get rule
     *
     * @return string 
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * Set text
     *
     * @param string $text
     *
     * @return ESuggestion
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set element
     *
     * @param string $element
     *
     * @return ESuggestion
     */
    public function setElement($element)
    {
        $this->element = $element;

        return $this;
    }

    /**
     * Get element
     *
     * @return string 
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * Set useLike
     *
     * @param string $useLike
     *
     * @return ESuggestion
     */
    public function setUseLike($useLike)
    {
        $this->useLike = $useLike;

        return $this;
    }

    /**
     * Get useLike
     *
     * @return string 
     */
    public function getUseLike()
    {
        return $this->useLike;
    }

    /**
     * Set forder
     *
     * @param integer $forder
     *
     * @return ESuggestion
     */
    public function setForder($forder)
    {
        $this->forder = $forder;

        return $this;
    }

    /**
     * Get forder
     *
     * @return integer 
     */
    public function getForder()
    {
        return $this->forder;
    }

    /**
     * Set position
     *
     * @param string $position
     *
     * @return ESuggestion
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
    * Devuelve la posición de una sugerencía
    * 
    * @return String
    */
    public function getPosition() {
        if ($this->position == null) return 'n';
        return $this->position;
    }

    /**
     * Set delay
     *
     * @param integer $delay
     *
     * @return ESuggestion
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Get delay
     *
     * @return integer
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * Set role
     *
     * @param string $role
     *
     * @return ESuggestion
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set panel
     *
     * @param string $panel
     *
     * @return ESuggestion
     */
    public function setPanel($panel)
    {
        $this->panel = $panel;

        return $this;
    }

    /**
     * Get panel
     *
     * @return string
     */
    public function getPanel()
    {
        return $this->panel;
    }

    /**
     * Set style
     *
     * @param string $style
     *
     * @return ESuggestion
     */
    public function setStyle($style)
    {
        $this->style = $style;

        return $this;
    }

    /**
     * Get style
     *
     * @return string
     */
    public function getStyle()
    {
        return $this->style;
    }
}
