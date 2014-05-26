<?php

namespace Trazeo\BaseBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class Helper {
	private $_container;
				
	function __construct(ContainerInterface $container) {
		$this->_container = $container;
	}
	
	function getCities($city, $limit = 10) {
		$em = $this->_container->get("doctrine.orm.entity_manager");

		$reJJ = $em->getRepository("JJsGeonamesBundle:City");
		
		$query = $reJJ->createQueryBuilder('a');
		
		$query->select('a.nameUtf8')
		->where('a.nameUtf8 LIKE :name')
		->setParameter('name', '%'.$city.'%')
		->setMaxResults($limit)
		->addOrderBy('a.id');
		
		$cities = $query->getQuery()->getResult();
		
		// TODO: Ordenar de mayor a menor coincidencia
		
		return $cities;		
	}
}