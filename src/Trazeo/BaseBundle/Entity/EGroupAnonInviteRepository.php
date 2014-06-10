<?php
	namespace Trazeo\BaseBundle\Entity;
	use Doctrine\ORM\EntityRepository;
	use Trazeo\BaseBundle\Entity\EGroup;
	 
	class EGroupAnonInviteRepository extends EntityRepository
	{
		public function createNew(EGroup $group, $userEmail, $fos_user_current, $con) {
			$em = $this->getEntityManager();
			
			$eGAI = new EGroupAnonInvite();
			$eGAI->setEmail($userEmail);
			$eGAI->setGroup($group);
			
			$em->persist($eGAI);
			$em->flush();
			
			$link = $con->get('router')->generate('home_invite_user', array('email' => $eGAI->getEmail(), 'token' => $eGAI->getToken(), 'id' => $eGAI->getId()));
			
			//echo $link;
			//exit();
			
			// Mandar email de invitación a Usuario
			$message = \Swift_Message::newInstance()
			// TODO: Traducir
			->setSubject("Ha sido invitado al sistema de Trazeo")
			->setFrom(array("info@trazeo.com" => "Trazeo"))
			->setTo($userEmail)
			->setBody($con->get('templating')->render('TrazeoFrontBundle:PanelGroups:email_invite.html.twig', array('link' => $link, 'group' => $group, 'user' => $fos_user_current)), 'text/html');
			$ok = $con->get('mailer')->send($message);

			return $eGAI;
		}	
		
		public function supportsClass($class) {
			return $class === 'Trazeo\BaseBundle\Entity\EGroupAnonInvite';
		}
	}
?>