<?php

namespace UserBundle\Repository;

use CoreBundle\Repository\AbstractDateRepository;
use Doctrine\ORM\QueryBuilder;

class UserRepository extends AbstractDateRepository
{
    /**
     * @param QueryBuilder $qb
     * @param string|array $username
     *
     * @return QueryBuilder
     */
    public function filterByUsername(QueryBuilder $qb, $username) : QueryBuilder
    {
        return $this->filterBy($qb, 'username', $username);
    }
}
