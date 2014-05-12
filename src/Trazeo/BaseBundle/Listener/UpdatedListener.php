<?php
namespace Trazeo\BaseBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Trazeo\BaseBundle\Entity\UserExtend;
use Application\Sonata\UserBundle\Entity\User as FOSUser;
use Trazeo\BaseBundle\Entity\EEvent;

class UpdatedListener implements EventSubscriber {
	public function getSubscribedEvents()
	{
		return array(
				'postPersist',
				'postUpdate'
		);
	}	

	public function postPersist(LifecycleEventArgs $args) {
		$this->execUpdate($args);
	}
	public function postUpdate(LifecycleEventArgs $args) {
		$this->execUpdate($args);
	}
	
	public function execUpdate($args) {
		$entity = $args->getEntity();
		$em = $args->getEntityManager();
		
		if ($entity instanceof EEvent){
			$ride = $entity->getRide();
			if($ride != null){
				$ride->setUpdatedAt(new \DateTime());
				$em->persist($ride);
				$em->flush();
			}
		}
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