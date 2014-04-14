<?php

namespace Trazeo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity Route
 *
 * @ORM\Table("e_points_route")
 * @ORM\Entity
 */
class EPointsRoute
{
	use \Sopinet\Bundle\SimplePointBundle\Model\GeoLocation;
	/**
	 * @var integer
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
