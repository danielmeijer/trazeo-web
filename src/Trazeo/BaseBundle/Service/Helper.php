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
	 *
	 *
	 */
	function getAutoLoginUrl($user,$name,$args=null) {
		if($args!=null)$path=$this->_container->get('router')->generate($name, $args);
		else $path=$this->_container->get('router')->generate($name);
		$pos=strpos($path,'panel/');
		$path = substr($path, $pos);
		$path =$this->_container->get('urlhelper')->generateUrl($path,$user);
		$pos=strpos($path,'auto/');
		$path = substr($path, $pos);
		return $path;
	}

    function getPageBySubdomain($subdomain = null) {
        // TODO: DEBUG
        if ($_SERVER['HTTP_HOST'] == "localhost" && $subdomain == null) {
            $subdomain = "torrelodones";
        }

        if ($subdomain == null) {
            $parts=explode('.', $_SERVER["SERVER_NAME"]);
            $subdomain='';
            foreach ($parts as $part) {
                if($part!='trazeo') {
                    if($subdomain!=null) {
                        $subdomain .= '.';
                    }
                    $subdomain .= $part;
                }
            }

        }

        if ($subdomain == "beta" || $subdomain == "app") {
            return null;
        }

        $em = $this->_container->get("doctrine.orm.entity_manager");
        $repositoryPage = $em->getRepository("TrazeoMyPageBundle:Page");
        $page = $repositoryPage->findOneBySubdomain($subdomain);

        return $page;
    }

    function getDateTime($datetime_string) {
        $pos = strpos($datetime_string, "/");
        if ($pos !== false) {
            $temp_arr = explode("/", $datetime_string);
            $datetime_string_good = $temp_arr[2] . "-" . $temp_arr[1] . "-" . $temp_arr[0];
        } else {
            $datetime_string_good = $datetime_string;
        }
        return new \DateTime($datetime_string_good, new \DateTimeZone('Europe/Madrid'));
    }

    function getDataFromRides($rides) {
        // Filtramos paseos que no son de prueba (más de 1 metro)
        $rides_good = array();
        /** @var ERide $ride */
        foreach($rides as $ride) {
            if ($ride->getDistance() > 0) {
                $rides_good[] = $ride;
            }
        }

        /**
         * no total de kilómetros recorridos por los niños,
         * no total de paseos,
         * no total de participaciones en el proyecto,
         * kg. de CO2 evitados si todos esos trayectos se hubieran realizado en coche,
         * combustible y euros ahorrados por las familias,
         * tiempo total caminado por los niños
         *
         *
         *
         *  $("#enviroment_resume").html((distance*0.0001).toFixed(2)+" litros de carburante");
        $("#safe_resume").html((distance*0.0001*1.5).toFixed(2)+" € en carburante");
        $("#pollution_resume").html((distance*0.0001*0.4).toFixed(2)+" kg");
         *
         */

        $data = array();
        $temp_metros = 0;
        $data['participaciones'] = 0;
        $temp_tiempo = 0;

        foreach($rides_good as $ride) {
            $children_count = $ride->getCountChildsR();
            $data['participaciones'] += $children_count;
            $temp_metros += $ride->getDistance();

            // Sumamos el tiempo en segundos
            $temp_tiempo += $ride->getDurationSeconds() * $children_count;
        }

        $data['paseos'] = count($rides_good);
        $data['km'] = round($temp_metros * 0.001, 2);
        $data['co2'] = $data['km'] * 0.4;
        // TODO: OJO CON LOS LITROS CONSUMIDOS
        $data['litros_combustible'] = round($data['km'] / 12.5, 2);
        $data['euros_combustible'] = $data['litros_combustible'] * 1.4;

        // Ponemos el tiempo en el formato correcto
        $temp_tiempo_date = new \DateTime();
        $temp_tiempo_date->setTimestamp($temp_tiempo);

        $t = new \DateTime();
        $t->setTimestamp(0);

        $temp_tiempo_diff = $temp_tiempo_date->diff($t);
        $data['tiempo_formato'] = $temp_tiempo_diff->s . " segundos.";
        if ($temp_tiempo_diff->i > 0) {
            $data['tiempo_formato'] = $temp_tiempo_diff->i . " minutos, " . $data['tiempo_formato'];
        }
        if ($temp_tiempo_diff->h > 0) {
            $data['tiempo_formato'] = $temp_tiempo_diff->h . " horas, " . $data['tiempo_formato'];
        }
        if ($temp_tiempo_diff->d > 0) {
            $data['tiempo_formato'] = $temp_tiempo_diff->d . " días, " . $data['tiempo_formato'];
        }
        if ($temp_tiempo_diff->m > 0) {
            if ($temp_tiempo_diff->m == 1) $str = "mes";
            else $str = "meses";
            $data['tiempo_formato'] = $temp_tiempo_diff->m . " ".$str.", " . $data['tiempo_formato'];
        }
        if ($temp_tiempo_diff->y > 0) {
            $data['tiempo_formato'] = $temp_tiempo_diff->y . " años, " . $data['tiempo_formato'];
        }

        return $data;
    }
}