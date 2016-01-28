<?php

namespace Trazeo\MyPageBundle\Twig;

use Symfony\Component\Locale\Locale;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Trazeo\BaseBundle\Entity\EGroup;

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
        	'getGroup' => new \Twig_Filter_Method($this, 'getGroup'),
        );
    }

	public function getGroup($group_id) {
		$em = $this->container->get('doctrine')->getEntityManager();
        $repositoryGroup = $em->getRepository('TrazeoBaseBundle:EGroup');
        /** @var EGroup $group */
        $group = $repositoryGroup->findOneById($group_id);
        if ($group == null) return "";
        else return $group->getName();
	}
    
    public function getName()
    {
        return 'Trazeo_extension';
    }
}