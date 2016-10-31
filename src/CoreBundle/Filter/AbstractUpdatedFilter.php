<?php

namespace CoreBundle\Filter;

abstract class AbstractUpdatedFilter extends AbstractCreatedFilter
{
    const UPDATED_BEFORE = 'updated_before';
    const UPDATED_AFTER  = 'updated_after';

    /**
     * @inheritdoc
     */
    protected function getMapping() : array
    {
        return array_merge(
            parent::getMapping(),
            [
                self::UPDATED_BEFORE => [$this->repo, 'filterByUpdatedBefore'],
                self::UPDATED_AFTER  => [$this->repo, 'filterByUpdatedAfter'],
            ]
        );
    }

    /**
     * @inheritdoc
     */
    protected function getMappingValidate() : array
    {
        return array_merge(
            parent::getMappingValidate(),
            [
                self::UPDATED_BEFORE => [$this, 'validateTimestamp'],
                self::UPDATED_AFTER  => [$this, 'validateTimestamp'],
            ]
        );
    }
}
