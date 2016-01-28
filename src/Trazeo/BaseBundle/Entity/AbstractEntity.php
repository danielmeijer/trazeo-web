<?php

namespace Trazeo\BaseBundle\Entity;

/**
 * Abstract Entity
 */
abstract class AbstractEntity
{
    /**
     * Return the actual entity repository
     *
     * @return entity repository or null
     */
    protected function getRepository()
    {
        global $kernel;

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }

        $annotationReader = $kernel->getContainer()->get('annotation_reader');

        $object = new \ReflectionObject($this);

        if ($configuration = $annotationReader->getClassAnnotation($object, 'Doctrine\ORM\Mapping\Entity')) {
            if (!is_null($configuration->repositoryClass)) {
                $repository = $kernel->getContainer()->get('doctrine.orm.entity_manager')->getRepository(get_class($this));

                return $repository;
            }
        }

        return null;

    }

    protected function getEntityManager() {
        global $kernel;

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }

        $annotationReader = $kernel->getContainer()->get('annotation_reader');

        $object = new \ReflectionObject($this);

        if ($configuration = $annotationReader->getClassAnnotation($object, 'Doctrine\ORM\Mapping\Entity')) {
            if (!is_null($configuration->repositoryClass)) {
                $em = $kernel->getContainer()->get('doctrine.orm.entity_manager');

                return $em;
            }
        }

        return null;
    }
}