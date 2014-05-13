<?php

namespace Trazeo\BaseBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Trazeo\BaseBundle\Entity\ERoute;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadRouteData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $manager;
	
	public function createRoute($userextend, $name)
	{
		$route = new ERoute();
		$route->setName($name);
		$route->setAdmin($userextend);
		
		$this->manager->persist($route);
		$this->manager->flush();
		$this->addReference("route_".$name, $route);
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
		
		$this->createRoute($userExtend, "ruta_1");
		$this->createRoute($userExtend, "ruta_2");
		$this->createRoute($userExtend, "ruta_3");
		$this->createRoute($userExtend, "ruta_4");
		$this->createRoute($userExtend, "ruta_5");
	}
	
	public function getOrder(){
		return 2;
	}
}