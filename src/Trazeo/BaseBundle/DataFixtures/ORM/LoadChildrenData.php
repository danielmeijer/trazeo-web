<?php

namespace Trazeo\BaseBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Trazeo\BaseBundle\Entity\Children;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadChildrenData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $manager;
	
	/**
	 * Creamos un usuario extend para el usuario trazeo
	 * @param unknown $nick Nick del usuario
	 */
	public function createChildren($userExtend, $nick)
	{
		$children = new Children();
		$children->setNick($nick);
		$children->addUserextendchild($userExtend);
		$children->setDateBirth(new \DateTime("now"));
		$children->setVisibility(true);
		$children->setSex("H");
		$userExtend->addChild($children);
		
		$this->manager->persist($userExtend);
		$this->manager->persist($children);
		$this->manager->flush();
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$this->manager = $manager;
		
		$reUser = $this->manager->getRepository("Application\Sonata\UserBundle\Entity\User");
		$reUserExtend = $this->manager->getRepository("TrazeoBaseBundle:UserExtend");
		
		$user = $reUser->findOneByUsername("trazeo");
		
		$userExtend = $reUserExtend->findOneByUser($user);
		
		$this->createChildren($userExtend, "trazeo_niño_1");
		$this->createChildren($userExtend, "trazeo_niño_2");
		$this->createChildren($userExtend, "trazeo_niño_3");
		$this->createChildren($userExtend, "trazeo_niño_4");
		$this->createChildren($userExtend, "trazeo_niño_5");
	}
	
	public function getOrder(){
		return 2;
	}
}