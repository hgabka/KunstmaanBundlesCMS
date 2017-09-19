<?php

namespace Kunstmaan\TranslatorBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Translator Repository class.
 */
class AbstractTranslatorRepository extends EntityRepository
{
    public function flush($entity = null)
    {
        if (null !== $entity) {
            $this->getEntityManager()->persist($entity);
        }

        $this->getEntityManager()->flush();
    }
}
