<?php

namespace CoreBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

abstract class AbstractRepository extends EntityRepository
{
    /**
     * @param QueryBuilder $qb
     *
     * @return string
     */
    public function getAlias(QueryBuilder $qb) : string
    {
        return $qb->getRootAliases()[0] . '.';
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $attribute
     * @param string|array $value
     *
     * @return QueryBuilder
     */
    public function filterBy(QueryBuilder $qb, string $attribute, $value) : QueryBuilder
    {
        $alias = $this->getAlias($qb);

        if (is_array($value)) {
            $expr = $qb->expr()->in($alias . $attribute, ':' . $attribute);
        } else {
            $expr = $qb->expr()->like($alias . $attribute, ':' . $attribute);
            $value = '%' . $value . '%';
        }

        return $qb
            ->andWhere($expr)
            ->setParameter($attribute, $value);
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $orderBy
     * @param              $order
     *
     * @return QueryBuilder
     */
    public function applyOrder(QueryBuilder $qb, string $orderBy, string $order) : QueryBuilder
    {
        $alias = $this->getAlias($qb);

        return $qb->orderBy($alias . $orderBy, $order);
    }
}
