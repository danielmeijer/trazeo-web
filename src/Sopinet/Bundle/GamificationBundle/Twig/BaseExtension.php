<?php

namespace Sopinet\Bundle\GamificationBundle\Twig;

use Symfony\Component\Locale\Locale;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Twig Extension - SopinetGamificationBundle
 */
class BaseExtension extends \Twig_Extension implements ContainerAwareInterface
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }   

    /**
     * Class constructor
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container the service container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }
    
    public function getFilters()
    {
        return array(
        	'getSopinetUserAction' => new \Twig_Filter_Method($this, 'getSopinetUserActionFilter')
        );
    }
	
	/**
	 * Devuelve las target entities de una accion en forma de cadena
	 * @param UserAction <Entity> $useraction
	 * @return Array Target entities _tostring
	 */
	public function getSopinetUserActionFilter($useraction) {
		$em = $this->container->get('doctrine')->getEntityManager();
		$gam = $this->container->get('sopinet_gamification');
		return $gam->actionToString($useraction);
	}
    
    public function getName()
    {
        return 'SopinetGamification_extension';
    }
}