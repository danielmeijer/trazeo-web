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
		$this->execUpdate($args, 'persist');
	}
	public function postUpdate(LifecycleEventArgs $args) {
		$this->execUpdate($args, 'update');
	}
	
	public function execUpdate($args, $action) {
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
		// Sólo se ejecuta la primera vez que guardamos el usuario (persist), no en cada Update
		if ($entity instanceof FOSUser && $action == 'persist') {
		
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