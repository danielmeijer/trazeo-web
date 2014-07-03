<?php

namespace Sopinet\Bundle\UserNotificationsBundle\Twig;

use Symfony\Component\Locale\Locale;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sopinet\Bundle\UserNotificationsBundle\Util\TimeAgoHelper;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;

/**
 * Twig Extension - SopinetUserNotificationsBundle
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
        	'getSopinetUserNotifications' => new \Twig_Filter_Method($this, 'getSopinetUserNotificationsFilter'),
        	'parseSopinetUserNotification' => new \Twig_Filter_Method($this, 'parseSopinetUserNotificationFilter'),
        	'getTimeAgo'  => new \Twig_Filter_Method($this, 'getTimeAgoFilter'),
        );
    }
	
	/**
	 * Devuelve las notificaciones para el Usuario indicado (o para el logueado si no se indica)
	 * @param User <Entity> $user
	 * @return Mix Notifications
	 */
	public function getSopinetUserNotificationsFilter($user = null, $limit = 5) {
		$em = $this->container->get('doctrine')->getEntityManager();
		$not  = $this->container->get('sopinet_user_notification');
		return $not->getNotifications($user, $limit);
	}
	
	public function parseSopinetUserNotificationFilter($notification, $action = "description") {
		$not  = $this->container->get('sopinet_user_notification');
		return $not->parseNotification($notification, $action);
	}
	
	public function getTimeAgoFilter($value, $format = 'Y-m-d H:s') {
		if ($value == null) return ""; // Devolvemos "" si es nulo
		if(!($value instanceof \DateTime)) {
			$transformer = new DateTimeToStringTransformer(null, null, $format);
			$value = $transformer->reverseTransform($value);
		}
		return new TimeAgoHelper($value, null, $this->container);
	}	
    
    public function getName()
    {
        return 'SopinetUserNotifications_extension';
    }
}