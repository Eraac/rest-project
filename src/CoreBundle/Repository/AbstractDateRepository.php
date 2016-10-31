<?php

namespace CoreBundle\Repository;

use Doctrine\ORM\QueryBuilder;

abstract class AbstractDateRepository extends AbstractRepository
{
    /**
     * @param QueryBuilder $qb
     * @param int|string   $timestamp
     *
     * @return QueryBuilder
     */
    public function filterByCreatedBefore(QueryBuilder $qb, $timestamp) : QueryBuilder
    {
        $alias = $this->getAlias($qb);

        $qb
            ->andWhere($alias . 'createdAt < :before')
            ->setParameter('before', $this->dateFromTimestamp($timestamp))
        ;

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param int|string   $timestamp
     *
     * @return QueryBuilder
     */
    public function filterByCreatedAfter(QueryBuilder $qb, $timestamp) : QueryBuilder
    {
        $alias = $this->getAlias($qb);

        $qb
            ->andWhere($alias . 'createdAt > :after')
            ->setParameter('after', $this->dateFromTimestamp($timestamp))
        ;

        return $qb;
    }

    /**
     * @param int|string $timestamp
     *
     * @return bool|\DateTime
     */
    protected function dateFromTimestamp($timestamp)
    {
        return \DateTime::createFromFormat('U', $timestamp);
    }
}
