<?php

namespace CoreBundle\Controller;

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

        // avoid return 200/201 when json is empty
        if (empty(json_decode($request->getContent(), true))) {
            return new JsonResponse(['errors' => [$this->t('core.error.empty_json')]], JsonResponse::HTTP_BAD_REQUEST);
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
}
