<?php

namespace Trazeo\MyPageBundle\Filter;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Trazeo\MyPageBundle\Entity\Page;

/**
 * Filtro por Páginas para Trazeo (Se podría abstraer para hacerlo genérico)
 */

class PageFilter extends SQLFilter
{
    protected $entityManager;
    protected $disabled = array();

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if ($targetEntity->getName() != 'Trazeo\BaseBundle\Entity\EGroup') {
            return '';
        }

        if ($targetEntity->reflClass->hasMethod('getPage')) {
            $subdomain = $this->getSubdomain();
            if ($subdomain != null) {
                $em = $this->getEntityManager();
                $rePage = $em->getRepository("TrazeoMyPageBundle:Page");
                /** @var Page $page */
                $page = $rePage->findOneBy(array('subdomain' => $subdomain));
                return $targetTableAlias.'.page_id = '.$page->getId();
            } else {
                return '';
            }
        }

        return '';
    }

    protected function getSubdomain($subdomain = null) {
        // TODO: DEBUG
        if ($_SERVER['HTTP_HOST'] == "localhost" && $subdomain == null) $subdomain = "torrelodones";
        if ($subdomain == null) {
            $parts=explode('.', $_SERVER["SERVER_NAME"]);
            $subdomain = $parts[0];
        }

        if ($subdomain == "beta" || $subdomain == "app") {
            return null;
        }

        return $subdomain;
    }

    protected function getEntityManager()
    {
        if ($this->entityManager === null) {
            $refl = new \ReflectionProperty('Doctrine\ORM\Query\Filter\SQLFilter', 'em');
            $refl->setAccessible(true);
            $this->entityManager = $refl->getValue($this);
        }

        return $this->entityManager;
    }
}