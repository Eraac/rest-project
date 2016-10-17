<?php

namespace UserBundle\Controller;

use CoreBundle\Controller\AbstractApiController;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use UserBundle\Entity\User;
use UserBundle\Form\UserType;

class UserController extends AbstractApiController
{
    /**
     * Shortcu get UserManager
     *
     * @return UserManager
     */
    private function getUserManager() : UserManager
    {
        return $this->get('fos_user.user_manager');
    }

    /**
     * Add an user
     *
     * @param Request $request
     *
     * @return object|\Symfony\Component\HttpFoundation\JsonResponse
     *
     * @FOSRest\View(statusCode=Response::HTTP_CREATED)
     */
    public function postAction(Request $request)
    {
        $um = $this->getUserManager();

        $user = $um->createUser();

        return $this->form($request, UserType::class, $user, Request::METHOD_POST);
    }

    /**
     * Persist an entity
     *
     * @param User $entity
     */
    protected function persistEntity($entity)
    {
        $um = $this->getUserManager();

        $um->updateUser($entity, true);
    }
}
