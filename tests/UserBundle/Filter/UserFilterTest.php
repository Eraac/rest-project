<?php

namespace Tests\UserBundle\Filter;

use CoreBundle\Filter\AbstractFilter;
use Doctrine\ORM\QueryBuilder;
use Tests\CoreBundle\Filter\AbstractDateFilterTest;
use UserBundle\Repository\UserRepository;

class UserFilterTest extends AbstractDateFilterTest
{
    protected function getFilter() : AbstractFilter
    {
        return $this->get('user.user_filter');
    }

    /**
     * @return QueryBuilder
     */
    protected function getQueryBuilder() : QueryBuilder
    {
        /** @var UserRepository $repo */
        $repo = $this->get('user.user_repository');

        return $repo->qbFindAll();
    }

    /**
     * @inheritDoc
     */
    protected function getCriterias() : array
    {
        return array_merge(
            parent::getCriterias(),
            ['username']
        );
    }

    /**
     * @inheritDoc
     */
    protected function getGoodValueCriterias() : array
    {
        return array_merge(
            parent::getGoodValueCriterias(),
            [['kevin', 'name']]
        );
    }

    /**
     * @inheritDoc
     */
    protected function getBadValueCriterias() : array
    {
        return array_merge(
            parent::getBadValueCriterias(),
            ['']
        );
    }

    /**
     * @inheritDoc
     */
    protected function getOrderBy() : array
    {
        return array_merge(
            parent::getOrderBy(),
            ['username']
        );
    }

    protected function getGoodValueOrderBy() : array
    {
        return array_merge(
            parent::getGoodValueOrderBy(),
            ['DESC']
        );
    }

    /**
     * @inheritDoc
     */
    protected function getBadValueOrderBy() : array
    {
        return array_merge(
            parent::getBadValueOrderBy(),
            ['BURK']
        );
    }
}
