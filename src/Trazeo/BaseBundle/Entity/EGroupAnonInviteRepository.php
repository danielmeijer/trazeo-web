<?php
	namespace Trazeo\BaseBundle\Entity;
	use Doctrine\ORM\EntityRepository;
	use Trazeo\BaseBundle\Entity\EGroup;
	use Swift_Message as Message;
	use Hip\MandrillBundle\Dispatcher;
	 
	class EGroupAnonInviteRepository extends EntityRepository
	{
		public function createNew(EGroup $group, $userEmail, $fos_user_current, $con) {
			$em = $this->getEntityManager();
			
			$eGAI = new EGroupAnonInvite();
			$eGAI->setEmail($userEmail);
			$eGAI->setGroup($group);
			$eGAI->setUserCreated($fos_user_current->getUserExtend());
			
			$em->persist($eGAI);
			$em->flush();
			
			$link = $con->get('router')->generate('home_invite_user', array('email' => $eGAI->getEmail(), 'token' => $eGAI->getToken(), 'id' => $eGAI->getId()));
			
			//echo $link;
			//exit();
	        //$dispatcher = $con->get('hip_mandrill.dispatcher');
            $mailer = $con->get('trazeo_mailer_helper');
            $message = $mailer->createNewMessage('hola@trazeo.es', 'Trazeo', $userEmail, "Ha sido invitado al sistema de Trazeo", $con->get('templating')->render('TrazeoFrontBundle:PanelGroups:email_invite.html.twig', array('link' => $link, 'group' => $group, 'user' => $fos_user_current)));
            $mailer->sendMessage($message);

			return $eGAI;
		}	
		
		public function supportsClass($class) {
			return $class === 'Trazeo\BaseBundle\Entity\EGroupAnonInvite';
		}
	}
?>