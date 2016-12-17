<?php

namespace Tests\CoreBundle\Filter;

use CoreBundle\Exception\InvalidFilterException;
use CoreBundle\Filter\AbstractFilter;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractFilterTest extends WebTestCase
{
    /**
     * @param string $name
     *
     * @return object
     */
    protected function get(string $name)
    {
        $container = static::createClient()->getContainer();

        return $container->get($name);
    }

    /**
     * @return QueryBuilder
     */
    abstract protected function getQueryBuilder() : QueryBuilder;

    /**
     * @return AbstractFilter
     */
    abstract protected function getFilter() : AbstractFilter;

    /**
     * @return array
     */
    protected function getCriterias() : array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getGoodValueCriterias() : array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getBadValueCriterias() : array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getOrderBy() : array
    {
        return ['id'];
    }

    protected function getGoodValueOrderBy() : array
    {
        return ['DESC'];
    }

    /**
     * @return array
     */
    protected function getBadValueOrderBy() : array
    {
        return ['BURK'];
    }


    public function testSuccessAllFilter()
    {
        $filter = $this->getFilter();
        $qb = $this->getQueryBuilder();

        $criterias = [];

        foreach ($this->getCriterias() as $key => $criteria) {
            $criterias[$criteria] = $this->getGoodValueCriterias()[$key];
        }

        foreach ($this->getOrderBy() as $key => $orderBy) {
            $criterias['_order'][$orderBy] = $this->getGoodValueOrderBy()[$key];
        }

        try {
            $filter->applyFilter($qb, $criterias);

            $qb->getQuery()->getResult();
        } catch (InvalidFilterException $e) {
            $this->assertTrue(false);
        }

        $this->assertTrue(true);
    }

    public function testFailAllFilter()
    {
        $filter = $this->getFilter();
        $qb = $this->getQueryBuilder();

        foreach ($this->getCriterias() as $key => $criteria) {
            if (!empty($this->getBadValueCriterias()[$key])) {
                $value = $this->getBadValueCriterias()[$key];

                $fail = $this->filterHasFailed($filter, $qb, [$criteria => $value]);

                $this->assertTrue($fail, sprintf(
                    'filter: %s | value: %s', $criteria, is_array($value) ? explode('[&]', $value) : $value
                ));
            }
        }

        foreach ($this->getOrderBy() as $key => $orderBy) {
            $value = $this->getBadValueOrderBy()[$key];

            $criterias = ['_order' => [$orderBy => $value]];

            $fail = $this->filterHasFailed($filter, $qb, $criterias);

            $this->assertTrue($fail, sprintf(
                'filter: %s | value: %s', $orderBy, $value
            ));
        }
    }

    /**
     * @param AbstractFilter $filter
     * @param QueryBuilder  $qb
     * @param array         $criterias
     *
     * @return bool
     */
    private function filterHasFailed(AbstractFilter $filter, QueryBuilder $qb, array $criterias) : bool
    {
        try {
            $filter->applyFilter($qb, $criterias);
        } catch (InvalidFilterException $e) {
            return true;
        }

        return false;
    }
}
