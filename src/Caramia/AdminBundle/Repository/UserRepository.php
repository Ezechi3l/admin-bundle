<?php

namespace Caramia\AdminBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * UserRepository
 */
class UserRepository extends EntityRepository
{

    /**
     * @param string $alias
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function getBuilder($alias = 'u')
    {
        return $this->createQueryBuilder($alias);
    }

    public function getUserByIdentifierQueryBuilder(QueryBuilder &$qb, $identifier)
    {
        $qb
            ->andWhere('u.email = :identifier')
            ->setParameter('identifier', $identifier)
        ;

        return $this;
    }

    public function getUserByEmail($identifier)
    {
        $qb = $this->getBuilder();
        $this->getUserByIdentifierQueryBuilder($qb, $identifier);

        return $qb->getQuery()->getOneOrNullResult();
    }
}
