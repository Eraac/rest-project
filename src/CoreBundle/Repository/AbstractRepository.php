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
     * @param string       $attribute
     * @param int|array    $value
     * @param string       $aliasJoin
     * @param string       $attributeJoin
     *
     * @return QueryBuilder
     */
    public function filterByWithJoin(QueryBuilder $qb, string $attribute, $value, string $aliasJoin, string $attributeJoin = 'id') : QueryBuilder
    {
        $this->safeLeftJoin($qb, $attribute, $aliasJoin);

        if (is_array($value)) {
            $expr = $qb->expr()->in($aliasJoin . '.' . $attributeJoin, ':' . $attribute);
        } else {
            $expr = $qb->expr()->eq($aliasJoin . '.' . $attributeJoin, ':' . $attribute);
        }

        return $qb
            ->andWhere($expr)
            ->setParameter($attribute, $value);
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $orderBy
     * @param string       $order
     * @param string       $alias
     *
     * @return QueryBuilder
     */
    public function applyOrder(QueryBuilder $qb, string $orderBy, string $order, string $alias = null) : QueryBuilder
    {
        $alias = $alias ?? $this->getAlias($qb);

        return $qb->orderBy($alias . $orderBy, $order);
    }

    /**
     * @param QueryBuilder $qb
     * @param string       $attribute
     * @param string       $aliasJoin
     */
    protected function safeLeftJoin(QueryBuilder $qb, string $attribute, string $aliasJoin)
    {
        $aliases = $qb->getAllAliases();

        if (!in_array($aliasJoin, $aliases)) {
            $alias = $this->getAlias($qb);

            $qb->leftJoin($alias . $attribute, $aliasJoin);
        }
    }
}
