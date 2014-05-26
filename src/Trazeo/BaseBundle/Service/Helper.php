<?php

namespace Trazeo\BaseBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class Helper {
	private $_container;
				
	function __construct(ContainerInterface $container) {
		$this->_container = $container;
	}
	
	/**
	 * Get cities from city String
	 * 
	 * @param String $city
	 * @param number $limit
	 * @param Boolean $objects
	 * @return multitype:Array of City or Array of Strings
	 */
	function getCities($city, $limit = 10, $objects = false) {
		$em = $this->_container->get("doctrine.orm.entity_manager");

		$reJJ = $em->getRepository("JJsGeonamesBundle:City");
		
		$query = $reJJ->createQueryBuilder('a');
		
		$query->select("a.id, a.nameUtf8")
		->where('a.nameUtf8 LIKE :name')
		->setParameter('name', '%'.$city.'%')
		->setMaxResults($limit)
		->addOrderBy('a.id');
		
		$cities = $query->getQuery()->getArrayResult();
		
		// TODO: Ordenar de mayor a menor coincidencia
		
		if ($objects) {
			$cities_old = $cities;
			$cities = array();
			foreach($cities_old as $c) {
				$cities[] = $reJJ->findOneById($c['id']);
			}
		}
		
		return $cities;		
	}
}