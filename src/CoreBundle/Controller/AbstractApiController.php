<?php

namespace CoreBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AbstractApiController extends FOSRestController implements ClassResourceInterface
{
    /**
     * @return ObjectManager
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
    protected function form(Request $request, $formType, $entity, $method)
    {
        $form = $this->createForm($formType, $entity, ['method' => $method]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->formSuccess($entity);
        }

        // avoid return 201 when json is empty
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
     * Dispatch an event
     *
     * @param string $name
     * @param Event $event
     */
    protected function dispatch($name, Event $event)
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
    protected function t($message, array $parameters = [], $domain = 'messages') : string
    {
        return $this->get('translator')->trans($message, $parameters, $domain);
    }
}
