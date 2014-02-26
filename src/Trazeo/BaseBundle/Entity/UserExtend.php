<?php

namespace Trazeo\BaseBundle\Entity;
use Doctrine\ORM\Mapping as ORM;

/**
 * UserExtend
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class UserExtend
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    
    /**
     * @ORM\OneToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
     */
    private $user;
}
