<?php

/**
 * This file is part of the <name> project.
 *
 * (c) <yourname> <youremail>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Application\Sonata\UserBundle\Entity;

use Sonata\UserBundle\Entity\BaseUser as BaseUser;

/**
 * This file has been generated by the Sonata EasyExtends bundle ( http://sonata-project.org/bundles/easy-extends )
 *
 * References :
 *   working with object : http://www.doctrine-project.org/projects/orm/2.0/docs/reference/working-with-objects/en
 *
 * @author <yourname> <youremail>
 */
class User extends BaseUser
{
    /**
     * @var integer $id
     */
	
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nick", type="string", length=255)
     */
    
    private $nick;
    
    /**
     * @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\City")
     */
    
    private $city;
    
    /**
     * @ORM\ManyToOne(targetEntity="JJs\Bundle\GeonamesBundle\Entity\Country")
     */
    
    protected $country;
    
    /**
     * @ORM\ManyToOne(targetEntity="trazeo\Trazeo\BaseBundle\Entity\Children", inversedBy="Children")
     */
    private $childrens;
    
    /**
     * @ORM\ManyToMany(targetEntity="Group")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     */
    protected $groups;
    
    /**
     * @ORM\OneToMany(targetEntity="Group")
     */
    protected $adminGroups;
    
    /**
     * @ORM\OneToMany(targetEntity="trazeo\Trazeo\BaseBundle\Entity\Route")
     */
    protected $adminRoutes;
    
    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }
}