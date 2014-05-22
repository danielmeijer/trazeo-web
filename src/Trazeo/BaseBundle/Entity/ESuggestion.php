<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
/**
 * Entity ESuggestion
 *
 * @ORM\Table("e_suggestion")
 * @ORM\Entity
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
	 * @var string
	 *
	 * @ORM\Column(name="element", type="string", length=255)
	 */
	protected $element;
	
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="order", type="integer", length=10)
	 */
	protected $order;	
	
	/**
	 * @var string
	 *
	 * @ORM\Column(name="element", type="string", length=255)
	 */	
	protected $element;

	/**
	 * @var string
	 * TODO: TRADUCIR
	 * @ORM\Column(name="text", type="string", length=500)
	 */
	protected $text;
}
