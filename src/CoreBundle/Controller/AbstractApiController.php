<?php

namespace CoreBundle\Controller;

use CoreBundle\Exception\InvalidFilterException;
use CoreBundle\Filter\AbstractFilter;
use CoreBundle\Service\Paginator;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Hateoas\Representation\PaginatedRepresentation;
use Symfony\Component\Form\Form;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AbstractApiController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @return Object|ObjectManager
     */
    protected function getManager() : ObjectManager
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Handle form
     *
     * @param Request $request
     * @param string $formType
     * @param object $entity
     * @param string $method
     *
     * @return object|JsonResponse|Form
     */
    protected function form(Request $request, $formType, $entity, string $method)
    {
        $form = $this->createForm($formType, $entity, ['method' => $method]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->formSuccess($entity);
        }

        return $this->formError($request, $form);
    }

    /**
     * Avoid return 2xx if the body is empty and form isn't submit
     *
     * @param Request $request
     * @param Form    $form
     *
     * @return Form|JsonResponse
     */
    protected function formError(Request $request, Form $form)
    {
        if (empty(json_decode($request->getContent(), true))) {
            return new JsonResponse(['errors' => [$this->t('core.error.empty_json')]], JsonResponse::HTTP_BAD_REQUEST);
        }

        if ('json' !== $request->getContentType()) {
            return $this->createJsonError('core.error.bad_content_type', JsonResponse::HTTP_BAD_REQUEST);
        }

        return $form;
    }

    /**
     * Hook form success
     *
     * @param object $entity
     *
     * @return object
     */
    protected function formSuccess($entity)
    {
        $this->persistEntity($entity);

        return $entity;
    }

    /**
     * Persist an entity
     *
     * @param object $entity
     */
    protected function persistEntity($entity)
    {
        $em = $this->getManager();

        $em->persist($entity);
        $em->flush();
    }

    /**
     * @param QueryBuilder $qb
     * @param Request $request
     * @param array $routeParameters
     *
     * @return PaginatedRepresentation
     */
    protected function paginate(QueryBuilder $qb, Request $request, array $routeParameters = []) : PaginatedRepresentation
    {
        /** @var Paginator $paginator */
        $paginator = $this->get('core.paginator');

        return $paginator->paginate($qb, $request, $routeParameters);
    }

    /**
     * Add serializer group to the current request
     *
     * @param string $group
     */
    protected function addSerializerGroup(string $group)
    {
        $this->get('core.manager.serializer_groups')->addGroup($group);
    }

    /**
     * Dispatch an event
     *
     * @param string $name
     * @param Event $event
     */
    protected function dispatch(string $name, Event $event)
    {
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $dispatcher->dispatch($name, $event);
    }

    /**
     * Translate a message
     *
     * @param string $message
     * @param array $parameters
     * @param string $domain
     *
     * @return string
     */
    protected function t(string $message, array $parameters = [], string $domain = 'messages') : string
    {
        return $this->get('translator')->trans($message, $parameters, $domain);
    }

    /**
     * Shortcut get repository
     *
     * @param string $name
     *
     * @return ObjectRepository
     */
    protected function getRepository(string $name) : ObjectRepository
    {
        return $this->getDoctrine()->getRepository($name);
    }

    /**
     * @param string       $filter
     * @param QueryBuilder $qb
     * @param Request      $request
     *
     * @throws \Exception|InvalidFilterException
     *
     * @return QueryBuilder
     */
    protected function applyFilter(string $filter, QueryBuilder $qb, Request $request) : QueryBuilder
    {
        assert($this->has($filter), new \LogicException(sprintf('service %s doesn\'t exist !', $filter)));

        /** @var AbstractFilter $filter */
        $filter = $this->get($filter);

        try {
            $qb = $filter->applyFilter($qb, $request->query->get('filter', []));
        } catch (InvalidFilterException $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new InvalidFilterException(
                $this->t('core.error.wrong_filter'), $e
            );
        }

        return $qb;
    }

    /**
     * Shortcut return JsonResponse error
     *
     * @param string $message
     * @param int    $statusCode
     * @param array  $parameters
     * @param string $domain
     *
     * @return JsonResponse
     */
    protected function createJsonError(string $message, int $statusCode, array $parameters = [], string $domain = 'messages') : JsonResponse
    {
        $message = $this->t($message, $parameters, $domain);

        return $this->createRawJsonError($message, $statusCode);
    }

    /**
     * Shortcut return JsonResponse error without translating
     *
     * @param string|array $message
     * @param int $statusCode
     *
     * @return JsonResponse
     */
    protected function createRawJsonError($message, int $statusCode) : JsonResponse
    {
        if (!is_array($message)) {
            $message = [$message];
        }

        return new JsonResponse(
            ['errors' => $message],
            $statusCode
        );
    }
}
