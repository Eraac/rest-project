<?php

namespace Tests\CoreBundle\Filter;

use Doctrine\ORM\QueryBuilder;
use Tests\CoreBundle\Filter\AbstractFilterTest;
use UserBundle\Repository\UserRepository;

abstract class AbstractDateFilterTest extends AbstractFilterTest
{
    /**
     * @inheritDoc
     */
    protected function getCriterias() : array
    {
        return array_merge(
            parent::getCriterias(),
            ['created_before', 'created_after']
        );
    }

    /**
     * @inheritDoc
     */
    protected function getGoodValueCriterias() : array
    {
        return array_merge(
            parent::getGoodValueCriterias(),
            ['10000', '1477902562']
        );
    }

    /**
     * @inheritDoc
     */
    protected function getBadValueCriterias() : array
    {
        return array_merge(
            parent::getBadValueCriterias(),
            ['not-a-number', 'burk-number']
        );
    }
}
