<?php

namespace CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class HealthCheckController
 *
 * @package CoreBundle\Controller
 */
class HealthCheckController extends AbstractApiController
{
    /**
     * @FOSRest\Get("/health_check")
     *
     * @return JsonResponse
     */
    public function cgetAction() : JsonResponse
    {
        return new JsonResponse(['status' => 'ok']);
    }
}
