<?php

namespace UserBundle\Controller;

use FOS\RestBundle\Controller\Annotations as FOSRest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UserBundle\Entity\User;
use UserBundle\Form\UserEditType;

class MeController extends AbstractUserController
{
    /**
     * Return the current user
     *
     * @return User
     */
    public function getAction() : User
    {
        return $this->getUser();
    }

    /**
     * Update the current user
     *
     * @param Request $request
     *
     * @return JsonResponse|User
     */
    public function patchAction(Request $request)
    {
        $user = $this->getUser();

        return $this->form($request, UserEditType::class, $user, Request::METHOD_PATCH);
    }
}
