<?php
	namespace Sopinet\Bundle\SuggestionBundle\Entity;
	use Doctrine\ORM\EntityRepository;
	use Sopinet\Bundle\SuggestionBundle\Entity\ESuggestion;
	 
	class ESuggestionRepository extends EntityRepository
	{
		/**
		 * Obtiene todas las sugerencías para un usuario y un rol
		 * 
		 * @param User <Entity> $user
		 * @param Url 
		 * @return Array{ESuggestion} $sugs
		 */
		public function getSuggestionsFor($role, $panel) {
			$em = $this->getEntityManager();
			$reSu = $em->getRepository('SopinetSuggestionBundle:ESuggestion');
	    	$sugs = $reSu->findBy(
                array('role' => $role,'panel'=>$panel), 
                array('forder' => 'ASC')
            );
            return $sugs;
	    }
	}
?>