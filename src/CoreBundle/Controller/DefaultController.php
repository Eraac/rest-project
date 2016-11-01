<?php

namespace CoreBundle\Controller;

use CoreBundle\Entity\LogRequest;
use CoreBundle\EventListener\LogRequestListener;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @package CoreBundle\Controller
 *
 * @Security("has_role('ROLE_VIEW_LOG_REQUEST')")
 */
class DefaultController extends Controller
{
    public function indexAction()
    {
        // Not implemented yet (miss front)

        return $this->render('CoreBundle:Default:index.html.twig', [
            'routes'    => $this->getAllRoutes(),
            'methods'   => LogRequest::METHODS,
            'status'    => $this->getAllStatusCode(),
        ]);
    }

    private function getAllRoutes() : array
    {
        $router = $this->get('router');
        $allRoutes = $router->getRouteCollection();

        $routes = [];

        foreach ($allRoutes as $name => $route) {
            if (LogRequestListener::isLoggableRoute($name)) {
                $routes[] = $name;
            }
        }

        return $routes;
    }

    private function getAllStatusCode() : array
    {
        return [
            Response::HTTP_OK,
            Response::HTTP_CREATED,
            Response::HTTP_NO_CONTENT,
            Response::HTTP_BAD_REQUEST,
            Response::HTTP_UNAUTHORIZED,
            Response::HTTP_FORBIDDEN,
            Response::HTTP_NOT_FOUND,
            Response::HTTP_METHOD_NOT_ALLOWED,
            Response::HTTP_INTERNAL_SERVER_ERROR,
        ];
    }
}
