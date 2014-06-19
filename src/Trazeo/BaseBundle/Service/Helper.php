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
		
		if(!$objects)$param_return="a.id, a.nameUtf8";
		else $param_return="a";

		$query->select($param_return)
		->where('a.nameUtf8 LIKE :name')
		->setParameter('name', '%'.$city.'%')
		->setMaxResults($limit)
		->addOrderBy('a.id');
		
		$cities = $query->getQuery()->getArrayResult();

		// Función que compara los elementos segun su coincidencía
		function sorter($city) {
			
			return function ($a, $b) use ($city) {
				if(is_array($a)){
					similar_text($a['nameUtf8'],$city,$a_percent);
					similar_text($b['nameUtf8'],$city,$b_percent);
				}
				else{
					similar_text($a->getNameUtf8(),$city,$a_percent);
					similar_text($b->getNameUtf8(),$city,$b_percent);
				}
				if($a_percent==$b_percent){
					return 0;
				}
				else if($a_percent<$b_percent){
					return 1;
				}
				else{
					return -1;
				}
			};
		}
		usort($cities, sorter($city));
		
		//En caso de querer devolver un array de objetos lo construye 
		if ($objects) {
			$cities_old = $cities;
			$cities = array();
			foreach($cities_old as $c) {
				$cities[] = $reJJ->findOneById($c['id']);
			}
		}
		return $cities;		
	}
	
	/**
	 * Get child distance walked on a ride
	 *
	 * @param Child $child
	 * @param Ride $ride
	 * @return number: Distance in meters
	 */
	function getChildDistance($child, $ride) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		
		
		$events = $this->getChildSegments($child,$ride);

		$distance=0;
		for ($i = 0; $i < count($events); $i++) {
			for ($j = 0; $j < count($events[$i])-1; $j++) {
				$distance+=$this->getDistance($events[$i][$j],$events[$i][$j+1]);
			}
		}
		
		return $distance;
		
	}	

	/**
	 * Get points events between two events(in and out)
	 *
	 * @param Child $child
	 * @param Ride $ride
	 * @return multiple:All points between in and outs of the child
	 */
	private function getChildSegments($child,$ride) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');

		$query = $reEvent->createQueryBuilder('e')
		->where('e.data LIKE :name AND e.ride = :ride AND (e.action = :in OR e.action = :out)')
		->setParameters(array('name' => "%".$child->getNick()."%", 'ride' => $ride, 'in'=> 'in', 'out'=> 'out'))
		->orderBy('e.createdAt', 'ASC')
		->getQuery();
		
		$events = $query->getResult();
		$segments=[];
	
		for ($i = 0; $i < count($events); $i+=2) {
			$in=$events[$i]->getId();
			
			if ($i+1==count($events))$out=PHP_INT_MAX;
			else $out=$events[$i+1]->getId();
			
			array_push($segments,$this->getPointsEventsBetween($in,$out,$ride));
		}

			
	
		return $segments;
	}
	
	/**
	 * Get points events between two events(in and out)
	 *
	 * @param Id $in
	 * @param Id $out
	 * @param Ride $ride
	 * @return multiple:Points between events
	 */
	private function getPointsEventsBetween($in,$out,$ride) {
		$em = $this->_container->get("doctrine.orm.entity_manager");
		$reEvent = $em->getRepository('TrazeoBaseBundle:EEvent');
		 
		$query = $reEvent->createQueryBuilder('e')
		->where('e.id > :in AND e.id < :out AND e.ride = :ride AND e.action = :point')
		->setParameters(array('in' => $in,'out' => $out, 'ride' => $ride, 'point'=>'point'))
		->orderBy('e.createdAt', 'ASC')
		->getQuery();
		 
		 
		$events = $query->getResult();
		
		return $events;
	}
	/**
	 * @param ESimplePoint $point1
	 * @param ESimplePoint $point2
	 * @retun number:distance between two points(has an error about + o – 3 meters)
	 */
	private function getDistance($point1,$point2) {
		$R = 6371; // km (change this constant to get miles)
		$lat1=$point1->getLocation()->getLatitude();
		$lon1=$point1->getLocation()->getLongitude();
		$lat2=$point2->getLocation()->getLatitude();
		$lon2=$point2->getLocation()->getLongitude();
		$dLat = ($lat2-$lat1) * M_PI / 180;
		$dLon = ($lon2-$lon1) * M_PI / 180;
		$a = sin($dLat/2) * sin($dLat/2) +
		cos($lat1 * M_PI / 180 ) * cos($lat2 * M_PI / 180 ) *
		sin($dLon/2) * sin($dLon/2);
		$c = 2 * atan2(sqrt($a), sqrt(1-$a));
		$d = $R * $c;
		return round($d*1000);
	}
}