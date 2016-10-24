<?php

namespace UserBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class UserRepository extends EntityRepository
{
    public function qbFindAll() : QueryBuilder
    {
        return $this->createQueryBuilder('u');
    }
}
