<?php

namespace Trazeo\BaseBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Trazeo\BaseBundle\Entity\UserExtend;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadUserExtendData implements FixtureInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $manager;
	
	/**
	 * Creamos un usuario extend para el usuario trazeo
	 * @param unknown $nick Nick del usuario
	 */
	public function createUserExtend($nick)
	{
		
		//Creamos el usuario
		$user = new \Application\Sonata\UserBundle\Entity\User;
		$user->setEmail("trazeo@trazeo.es");
		$user->setPlainPassword("trazeo");
		$user->setUsername("trazeo");
		$user->setEnabled(1);
		
		$this->manager->persist($user);
		$this->manager->flush();
		
		/*$reUser = $this->manager->getRepository("\Application\Sonata\UserBundle\Entity\User");
		
		$user = $reUser->findOneById(1);
		
		$userExtend = new UserExtend();
		$userExtend->setUser($user);
		$userExtend->setNick($nick);
	
		$this->manager->persist($userExtend);
		$this->manager->flush();*/
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$this->manager = $manager;
		
		$this->createUserExtend("trazeo_niño_1");
	}
}