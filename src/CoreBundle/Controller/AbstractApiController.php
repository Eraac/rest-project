<?php

namespace CoreBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
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
     * @return object|JsonResponse
     */
    protected function form(Request $request, $formType, $entity, $method)
    {
        $form = $this->createForm($formType, $entity, ['method' => $method]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->formSuccess($entity);
        }

        return new JsonResponse($this->getErrorMessages($form), JsonResponse::HTTP_BAD_REQUEST);
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
     * Get all errors of a form
     *
     * @param Form $form
     *
     * @return array
     *
     * TODO use custom handler
     */
    protected function getErrorMessages(Form $form) : array
    {
        $errors = [];

        foreach ($form->getErrors() as $key => $error) {
            $errors[] = $error->getMessage();
        }

        /** @var Form $child */
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrorMessages($child);
            }
        }

        return $errors;
    }
}
