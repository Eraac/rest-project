<?php

namespace CoreBundle\Filter;

class LogRequestFilter extends AbstractCreatedFilter
{
    /**
     * @inheritdoc
     */
    protected function getMapping() : array
    {
        return array_merge(
            parent::getMapping(),
            [
                'route'  => [$this->repo, 'filterByRoute'],
                'path'   => [$this->repo, 'filterByPath'],
                'method' => [$this->repo, 'filterByMethod'],
                'query'  => [$this->repo, 'filterByQuery'],
                'status' => [$this->repo, 'filterByStatus'],
                'ip'     => [$this->repo, 'filterByIp'],
                'user'   => [$this->repo, 'filterByYser'],
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
                'route'  => [$this->repo, self::DEFAULT_METHOD_ORDER],
                'path'   => [$this->repo, self::DEFAULT_METHOD_ORDER],
                'method' => [$this->repo, self::DEFAULT_METHOD_ORDER],
                'status' => [$this->repo, self::DEFAULT_METHOD_ORDER],
                'ip'     => [$this->repo, self::DEFAULT_METHOD_ORDER],
                'user'   => [$this->repo, self::DEFAULT_METHOD_ORDER],
            ]
        );
    }
}
