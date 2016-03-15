<?php
namespace Trazeo\BaseBundle\Listener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use FOS\UserBundle\Document\Group;
use Sopinet\TimelineBundle\Entity\Comment;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Trazeo\BaseBundle\Entity\EGroup;
use Trazeo\BaseBundle\Entity\UserExtend;
use Application\Sonata\UserBundle\Entity\User as FOSUser;
use Trazeo\BaseBundle\Entity\EEvent;

class UpdatedListener implements EventSubscriber {
    private $_container;

    function __construct(ContainerInterface $container) {
        $this->_container = $container;
    }

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
        //Cuando se crea un nuevo comentario se crea las notificaciones pertinentes
        elseif($entity instanceof Comment && $action == 'persist'){
            //obtenemos el autor y el userextend asociado al autor
            $author=$entity->getAuthor();
            $authorUserExtend=$em->getRepository('TrazeoBaseBundle:UserExtend')->findOneByUser($author);
            //obtenemos el grupo(solo se puede obtener mediante la ruta del permalink)
            $group_id=explode('/',explode('/group/',$entity->getThread()->getPermalink())[1])[0];
            $group = $em->getRepository('TrazeoBaseBundle:EGroup')->findOneById($group_id);

            //obtenemos los usuarios del grupo y para todos los que no sean el autor del comentario se crea una notificación
            $userextends = $group->getUserextendgroups()->toArray();
            $not = $this->_container->get('sopinet_user_notification');
            foreach($userextends as $userextend)
            {
                $fos_reciver=$userextend->getUser();
                if($author!=$fos_reciver){
                    //generamos la url del autologin
                    $url=$this->_container->get('trazeo_base_helper')->getAutoLoginUrl($fos_reciver,'panel_group_timeline', array('id' => $group->getId()));
                    $not->addNotification(
                        "timeline.newFromMonitor",
                        "TrazeoBaseBundle:Userextend,SopinetTimelineBundle:Comment,TrazeoBaseBundle:EGroup",
                        $authorUserExtend->getId().",".(($entity->getId())).",".$group->getId(),
                        $url,
                        $userextend->getUser(),
                        null,
                        $this->_container->get('router')->generate('panel_group_timeline', array('id' => $group->getId()))
                    );
                }
            }
        } elseif( $entity instanceof EGroup && $action == 'update' ) {
            $chat = $entity->getChat();
            $users = $entity->getUserextendgroups();
            $chat->setChatMembers($users);
            $em->persist($chat);
            $em->flush($chat);
        }
	}
}