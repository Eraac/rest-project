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
            ->andWhere($alias . 'createdAt < :created_before')
            ->setParameter('created_before', $this->dateFromTimestamp($timestamp))
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
            ->andWhere($alias . 'createdAt > :created_after')
            ->setParameter('created_after', $this->dateFromTimestamp($timestamp))
        ;

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param int|string   $timestamp
     *
     * @return QueryBuilder
     */
    public function filterByUpdatedBefore(QueryBuilder $qb, $timestamp) : QueryBuilder
    {
        $alias = $this->getAlias($qb);

        $qb
            ->andWhere($alias . 'updatedAt < :update_before')
            ->setParameter('update_before', $this->dateFromTimestamp($timestamp))
        ;

        return $qb;
    }

    /**
     * @param QueryBuilder $qb
     * @param int|string   $timestamp
     *
     * @return QueryBuilder
     */
    public function filterByUpdatedAfter(QueryBuilder $qb, $timestamp) : QueryBuilder
    {
        $alias = $this->getAlias($qb);

        $qb
            ->andWhere($alias . 'updatedAt > :update_after')
            ->setParameter('update_after', $this->dateFromTimestamp($timestamp))
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
