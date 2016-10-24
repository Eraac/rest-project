<?php

namespace CoreBundle\Service;

use Doctrine\ORM\QueryBuilder;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Representation\PaginatedRepresentation;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

class Paginator
{
    const LIMIT = "_max_per_page";
    const PAGE  = "_page";
    const DEFAULT_LIMIT = 20;
    const HARD_LIMIT_PER_PAGE = 200;

    /**
     * @param QueryBuilder $qb
     * @param Request $request
     * @param array $routeParameters
     *
     * @return PaginatedRepresentation
     */
    public function paginate(QueryBuilder $qb, Request $request, array $routeParameters = []) : PaginatedRepresentation
    {
        $routeName = $request->get('_route');
        $criterias = $request->query->all();

        $pager = new Pagerfanta(new DoctrineORMAdapter($qb, false, false));

        $pager->setMaxPerPage($this->getLimitPerPage($criterias))
              ->setCurrentPage($this->getPage($criterias));

        $pff = new PagerfantaFactory(self::PAGE, self::LIMIT);

        return $pff->createRepresentation($pager, new Route($routeName, array_merge($criterias, $routeParameters)));
    }

    /**
     * @param array $criterias
     *
     * @return integer
     */
    private function getPage(array $criterias) : int
    {
        $page = $criterias[self::PAGE] ?? 1;

        // avoid negative number
        return $page >= 1 ? $page : 1;
    }

    /**
     * @param array $criterias
     *
     * @return integer
     */
    private function getLimitPerPage(array $criterias) : int
    {
        $limit = $criterias[self::LIMIT] ?? self::DEFAULT_LIMIT;

        // avoid negative number
        $limit = $limit >= 1 ? $limit : self::DEFAULT_LIMIT;

        // avoid too large limit
        return $limit > self::HARD_LIMIT_PER_PAGE ? self::DEFAULT_LIMIT : $limit;
    }
}
