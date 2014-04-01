<?php

namespace Trazeo\BaseBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Trazeo\BaseBundle\Entity\EGroup;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadGroupData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $manager;
	
	/**
	 * Creamos un usuario extend para el usuario trazeo
	 * @param unknown $nick Nick del usuario
	 */
	public function createTeam($manager, $userExtend, $name, $route, $child)
	{
		$team = new EGroup();
		$team->setName($name);
		$team->setRoute($manager->merge($this->getReference("route_".$route)));
		$team->setAdmin($userExtend);
		$team->addChild($manager->merge($this->getReference("child_".$child)));
		$this->manager->persist($userExtend);
		$this->manager->persist($team);
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
		
		$this->createTeam($manager, $userExtend, "trazeo_grupo_1", "ruta_1", "trazeo_niño_1");
		$this->createTeam($manager, $userExtend, "trazeo_grupo_2", "ruta_2", "trazeo_niño_2");
		$this->createTeam($manager, $userExtend, "trazeo_grupo_3", "ruta_3", "trazeo_niño_3");
		$this->createTeam($manager, $userExtend, "trazeo_grupo_4", "ruta_4", "trazeo_niño_4");
		$this->createTeam($manager, $userExtend, "trazeo_grupo_5", "ruta_5", "trazeo_niño_5");
	}
	
	public function getOrder(){
		return 3;
	}
}