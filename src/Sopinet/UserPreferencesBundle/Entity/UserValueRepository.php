<?php
	namespace Sopinet\UserPreferencesBundle\Entity;
	use Doctrine\ORM\EntityRepository;
	 
	class UserValueRepository extends EntityRepository
	{	
		/**
		 * Obtiene el valor de un usuario para una configuración
		 * Si existe la configuración la devuelve, sino, retorna el valor por defecto.
		 * 
		 * @param SopinetUserExtend <Entity> $sopinetuserextend
		 * @param UserSetting <Entity> $usersetting
		 * @return string Value
		 */
		public function getValue($sopinetuserextend, $usersetting) {
			$em = $this->getEntityManager();
			$reUserValue = $em->getRepository("SopinetUserPreferencesBundle:UserValue");
			$findUV = $reUserValue->findOneBy(array('user' => $sopinetuserextend, 'setting' => $usersetting));
			if ($findUV == null) {
				return $usersetting->getDefaultoption();
			} else {
				return $findUV->getValue();
			}
		}
		
		/**
		 * Guarda un valor de configuración para un usuario
		 * 
		 * @param User <Entity> $user
		 * @param Integer $usersetting_id
		 * @param String $value
		 * @return UserValue
		 */
		public function setValue($user, $usersetting_id, $value) {
			$em = $this->getEntityManager();
			$reUserSetting = $em->getRepository("SopinetUserPreferencesBundle:UserSetting");
			$usersetting = $reUserSetting->findOneById($usersetting_id);
			$reUserValue = $em->getRepository("SopinetUserPreferencesBundle:UserValue");
			$findUV = $reUserValue->findOneBy(array('user' => $user, 'setting' => $usersetting));
			if ($findUV == null) {
				$findUV = new UserValue();
				$findUV->setUser($user);
				$findUV->setSetting($usersetting);
			}
			$findUV->setValue($value);
			$em->persist($findUV);
			$em->flush();
			return $findUV;
		}
		
		public function getUsersWith($settingname, $value) {
			$em = $this->getEntityManager();
			$reUserSetting = $em->getRepository("SopinetUserPreferencesBundle:UserSetting");
			$usersetting = $reUserSetting->findOneByName($settingname);
			if ($value == $usersetting->getDefaultoption()) {
			
				$qb=$em->createQueryBuilder();
				
				$query = $em->createQuery(
						'SELECT u.id
    					FROM SopinetUserBundle:SopinetUserExtend u, SopinetUserPreferencesBundle:UserValue uv
    					WHERE uv.setting = :setting AND uv.value <> :default AND uv.user = u'
				)->setParameters(array('setting'=>$usersetting->getId(), 'default'=>$usersetting->getDefaultoption()));
			
				$nots = $query->getResult();

				if (count($nots) == 0) {
					$users = $qb->select('u')
					->from('SopinetUserBundle:SopinetUserExtend', 'u')
					->getQuery()
					->getResult();
				} else {
					// Array to String
					$nots_string = "";
					foreach($nots as $nk) {
						if ($nots_string != "") $nots_string .= ",";
						$nots_string .= $nk['id'];
					}
					
					$users = $qb->select('u')
					->from('SopinetUserBundle:SopinetUserExtend', 'u')
					->where($qb->expr()->notIn('u', $nots_string))
					->getQuery()
					->getResult();
				}
			
			} else {
				$query = $em->createQuery(
						'SELECT u
    					FROM SopinetUserBundle:SopinetUserExtend u, SopinetUserPreferencesBundle:UserValue uv
    					WHERE uv.setting = :setting AND uv.value = :select AND uv.user = u'
				)->setParameters(array('setting'=>$usersetting->getId(), 'select'=>$value));
				$users = $query->getResult();
			}			
			
			return $users;
		}
	}
?>