<?php
namespace Trazeo\BaseBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Trazeo\BaseBundle\Entity\UserExtend;


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
		if ($entity instanceof Attribute || 
			$entity instanceof Category ||
			$entity instanceof Config ||
			$entity instanceof Image ||
			$entity instanceof Item) {
			$museum = $entity->getMuseum();
			if ($museum != null) {
				$museum->setUpdatedAt(new \DateTime()); 
				$em->persist($museum);
				$em->flush();
			}			
		}
	}
}