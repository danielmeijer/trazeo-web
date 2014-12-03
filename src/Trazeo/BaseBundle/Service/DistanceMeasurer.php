<?php

namespace Trazeo\BaseBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class DistanceMeasurer {
	private $_container;
				
	function __construct(ContainerInterface $container) {
		$this->_container = $container;
	}
		
	/**
	 * Get child distance walked on a ride
	 *
	 * @param Child $child
	 * @param Ride $ride
	 * return number: Distance in meters
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
	 * return multiple:All points between in and outs of the child
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
	 * return multiple:Points between events
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
	 * return number:distance between two points(has an error about + o – 3 meters)
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