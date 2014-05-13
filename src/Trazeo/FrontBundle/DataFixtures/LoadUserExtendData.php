<?php

namespace Trazeo\BaseBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Trazeo\BaseBundle\Entity\UserExtend;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserExtendData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $manager;
	
	/**
	 * Creamos un usuario extend para el usuario trazeo
	 * @param unknown $nick Nick del usuario
	 */
	public function createUserExtend($name)
	{		
		//Creamos el usuario
		$user = new \Application\Sonata\UserBundle\Entity\User;
		$user->setEmail($name . "@" . $name . ".es");
		$user->setPlainPassword($name);
		$user->setUsername($name);
		$user->setEnabled(1);
		
		$this->manager->persist($user);
		$this->manager->flush();
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$this->manager = $manager;
		$this->createUserExtend("trazeo");
	}
	
	public function getOrder(){
		return 1;
	}
}