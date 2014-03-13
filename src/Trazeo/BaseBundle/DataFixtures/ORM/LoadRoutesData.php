<?php

namespace Trazeo\BaseBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Trazeo\BaseBundle\Entity\Routes;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadRoutesData extends AbstractFixture implements OrderedFixtureInterface
{
	/**
	 * @var ContainerInterface
	 */
	private $manager;
	
	public function createRoute($name)
	{
		$route = new Routes();
		$route->setName($name);
		
		$this->manager->persist($route);
		$this->manager->flush();
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function load(ObjectManager $manager)
	{
		$this->manager = $manager;
		
		$this->createRoute("ruta_1");
		$this->createRoute("ruta_2");
		$this->createRoute("ruta_3");
		$this->createRoute("ruta_4");
		$this->createRoute("ruta_5");
	}
	
	public function getOrder(){
		return 2;
	}
}