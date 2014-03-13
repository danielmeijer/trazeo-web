<?php

namespace Trazeo\BaseBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Trazeo\BaseBundle\Entity\Groups;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadGroupsData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $manager;
	
	/**
	 * Creamos un usuario extend para el usuario trazeo
	 * @param unknown $nick Nick del usuario
	 */
	public function createGroups($userExtend, $name)
	{
		$group = new Groups();
		$group->setName($name);
		$this->manager->persist($userExtend);
		$this->manager->persist($group);
		$this->manager->flush();
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$this->manager = $manager;
		
		//echo "Guardando grupos:";
		
		$reUser = $this->manager->getRepository("Application\Sonata\UserBundle\Entity\User");
		$reUserExtend = $this->manager->getRepository("TrazeoBaseBundle:UserExtend");
		
		$user = $reUser->findOneByUsername("trazeo");
		
		$userExtend = $reUserExtend->findOneByUser($user);
		
		$this->createGroups($userExtend, "trazeo_grupo_1");
		$this->createGroups($userExtend, "trazeo_grupo_2");
		$this->createGroups($userExtend, "trazeo_grupo_3");
		$this->createGroups($userExtend, "trazeo_grupo_4");
		$this->createGroups($userExtend, "trazeo_grupo_5");
	}
	
	public function getOrder(){
		return 2;
	}
}