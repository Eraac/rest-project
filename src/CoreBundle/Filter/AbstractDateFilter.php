<?php

namespace CoreBundle\Filter;

use CoreBundle\Exception\InvalidFilterException;

abstract class AbstractDateFilter extends AbstractFilter
{
    const CREATED_BEFORE = 'created_before';
    const CREATED_AFTER  = 'created_after';

    /**
     * @inheritdoc
     */
    protected function getMapping() : array
    {
        return array_merge(
            parent::getMapping(),
            [
                self::CREATED_BEFORE => [$this->repo, 'filterByCreatedBefore'],
                self::CREATED_AFTER  => [$this->repo, 'filterByCreatedAfter'],
            ]
        );
    }

    protected function getMappingValidate() : array
    {
        return array_merge(
            parent::getMappingValidate(),
            [
                self::CREATED_BEFORE => [$this, 'validateTimestamp'],
                self::CREATED_AFTER  => [$this, 'validateTimestamp'],
            ]
        );
    }

    /**
     * @param $timestamp
     *
     * @throws InvalidFilterException
     */
    private function validateTimestamp($timestamp)
    {
        // filter_var with this filter return number if is good, and 0 is a good int
        // ... so without '=== false', will get false instead of true for 0
        $valid = !(filter_var($timestamp, FILTER_VALIDATE_INT) === false);

        if (!$valid) {
            throw new InvalidFilterException(
                $this->t('core.error.filter')
            );
        }
    }
}
