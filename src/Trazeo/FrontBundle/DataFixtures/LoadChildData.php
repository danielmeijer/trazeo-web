<?php

namespace Trazeo\BaseBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Trazeo\BaseBundle\Entity\EChild;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadChildData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $manager;
	
	/**
	 * Creamos un usuario extend para el usuario trazeo
	 * @param unknown $nick Nick del usuario
	 */
	public function createChild($userExtend, $nick)
	{
		$child = new EChild();
		$child->setNick($nick);
		$child->addUserextendchild($userExtend);
		$child->setDateBirth(new \DateTime("now"));
		$child->setVisibility(true);
		$child->setGender("H");
		$userExtend->addChild($child);
		
		$this->manager->persist($userExtend);
		$this->manager->persist($child);
		$this->manager->flush();
		$this->addReference("child_".$nick, $child);
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
		
		$this->createChild($userExtend, "trazeo_niño_1");
		$this->createChild($userExtend, "trazeo_niño_2");
		$this->createChild($userExtend, "trazeo_niño_3");
		$this->createChild($userExtend, "trazeo_niño_4");
		$this->createChild($userExtend, "trazeo_niño_5");
	}
	
	public function getOrder(){
		return 2;
	}
}