<?php

namespace UserBundle\Controller;

use CoreBundle\Controller\AbstractApiController;
use FOS\UserBundle\Doctrine\UserManager;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UserBundle\Entity\User;

class AbstractUserController extends AbstractApiController
{
    /**
     * Persist an entity
     *
     * @param User $entity
     */
    protected function persistEntity($entity)
    {
        $this->updateUser($entity);
    }

    /**
     * Shortcut get UserManager
     *
     * @return UserManager
     */
    protected function getUserManager() : UserManager
    {
        return $this->get('fos_user.user_manager');
    }

    /**
     * Find an  by confirmation token
     *
     * @param string $token
     *
     * @throws NotFoundHttpException
     *
     * @return User
     */
    protected function findUserByToken($token) : User
    {
        $userManager = $this->getUserManager();

        /** @var User $user */
        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw $this->createNotFoundException(
                $this->t('confirmation.user.not_found', ['%token%' => $token])
            );
        }

        return $user;
    }

    /**
     * Update an user
     *
     * @param User $user
     */
    protected function updateUser(User $user)
    {
        $userManager = $this->getUserManager();

        $userManager->updateUser($user);
    }
}
