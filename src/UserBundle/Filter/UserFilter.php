<?php

namespace UserBundle\Filter;

use CoreBundle\Filter\AbstractDateFilter;

class UserFilter extends AbstractDateFilter
{
    /**
     * @inheritdoc
     */
    protected function getMapping() : array
    {
        return array_merge(
            parent::getMapping(),
            [
                'username' => [$this->repo, 'filterByUsername'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function getMappingOrderBy() : array
    {
        return array_merge(
            parent::getMappingOrderBy(),
            [
                'username' => [$this->repo, self::DEFAULT_METHOD_ORDER],
            ]
        );
    }
}
