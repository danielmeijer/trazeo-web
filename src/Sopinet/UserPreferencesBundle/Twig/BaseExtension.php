<?php

namespace Sopinet\UserPreferencesBundle\Twig;

use Symfony\Component\Locale\Locale;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Twig Extension - SopinetUserPreferencesBundle
 * Has a dependency to the apache intl module
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
        	'getUserValue' => new \Twig_Filter_Method($this, 'getUserValueFilter'),
        );
    }
	
	/**
	 * Devuelve el valor de configuración para un usuario y un campo setting
	 * @param User <Entity> $user
	 * @param UserSetting <Entity> $usersetting
	 * @return String value
	 */
	public function getUserValueFilter($user, $usersetting) {
		$em = $this->container->get('doctrine')->getEntityManager();
		$reUserValue = $em->getRepository("SopinetUserPreferencesBundle:UserValue");
		return $reUserValue->getValue($user, $usersetting);
	}
    
    public function getName()
    {
        return 'SopinetUserPreferences_extension';
    }
}