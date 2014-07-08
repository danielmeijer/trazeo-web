<?php
	namespace Sopinet\Bundle\UserNotificationsBundle\Entity;
	use Doctrine\ORM\EntityRepository;
	 
	class UserLiveRepository extends EntityRepository
	{	
		
		/**
		 * Devuelve el valor de configuración para un usuario o el valor por defecto
		 * 
		 * @param User <Entity> $user
		 * @return UserLive
		 */
		public function getValue($user) {
			$em = $this->getEntityManager();
			$reUserLive = $em->getRepository("SopinetUserNotificationsBundle:UserLive");
			$findUL = $reUserLive->findOneByUser($user);
			if ($findUL == null) {
				return $this->getContainer()->parameters['sopinet_user_notifications.default_live'];
			}
			else{
				return $findUL->getValue();
			}
		}

		/**
		 * Guarda un valor de configuración para un usuario
		 * 
		 * @param User <Entity> $user
		 * @param Integer $userlive_id
		 * @param String $value
		 * @return UserLive
		 */
		public function setValue($user, $value) {
			$em = $this->getEntityManager();

			$reUserLive = $em->getRepository("SopinetUserNotificationsBundle:UserLive");
			$findUL = $reUserLive->findOneByUser($user);
			if ($findUL == null) {
				$findUL = new UserLive();
				$findUL->setUser($user);
				$findUL->setValue($value);

			}
			else{
				$findUL->setValue($value);
			}
			$em->persist($findUL);
			$em->flush();
			return $findUL;
		}
	}
?>