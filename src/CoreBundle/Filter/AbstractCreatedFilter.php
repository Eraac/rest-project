<?php

namespace CoreBundle\Filter;

use CoreBundle\Exception\InvalidFilterException;

abstract class AbstractCreatedFilter extends AbstractFilter
{
    const UPDATED_BEFORE = 'created_before';
    const UPDATED_AFTER  = 'created_after';

    /**
     * @inheritdoc
     */
    protected function getMapping() : array
    {
        return array_merge(
            parent::getMapping(),
            [
                self::UPDATED_BEFORE => [$this->repo, 'filterByCreatedBefore'],
                self::UPDATED_AFTER  => [$this->repo, 'filterByCreatedAfter'],
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

    /**
     * @param $timestamp
     *
     * @throws InvalidFilterException
     */
    protected function validateTimestamp($timestamp)
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
