<?php
namespace Trazeo\BaseBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Trazeo\BaseBundle\Entity\UserExtend;
use Application\Sonata\UserBundle\Entity\User as FOSUser;


class UpdatedListener implements EventSubscriber {
	public function getSubscribedEvents()
	{
		return array(
				'postPersist',
		);
	}	

	public function postPersist(LifecycleEventArgs $args) {
		$this->execUpdate($args);
	}
	
	public function execUpdate($args) {
		$entity = $args->getEntity();
		$em = $args->getEntityManager();
		if ($entity instanceof FOSUser) {
		
			$email = $entity->getEmail();
			$userExtend = new UserExtend();
			
			$userExtend->setNick($email);
			$userExtend->setTutorial(0);
			$userExtend->setTutorialMap(0);
			$userExtend->setUser($entity);
			$em->persist($userExtend);
			$em->flush();
					
		}
	}
}