<?php

namespace UserBundle\Security;

use CoreBundle\Security\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use UserBundle\Entity\User;

class UserVoter extends AbstractVoter
{
    const ADD_ME = "addMe";

    /**
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject) : bool
    {
        return
            (parent::supports($attribute, $subject) || self::ADD_ME == $attribute)
            && $subject instanceof User;
    }

    /**
     * Return true is current user can view the $user
     *
     * @param User $user
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function canView(User $user, TokenInterface $token) : bool
    {
        return true;
    }

    /**
     * Return true if current user can create an user
     *
     * @param User $user
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function canCreate(User $user, TokenInterface $token) : bool
    {
        return true;
    }

    /**
     * Return true if current user can edit the $user
     *
     * @param User $user
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function canEdit(User $user, TokenInterface $token) : bool
    {
        return $this->isHimself($user, $token);
    }

    /**
     * Return true if current user can delete the $user
     *
     * @param User $user
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function canDelete(User $user, TokenInterface $token) : bool
    {
        return $this->isHimself($user, $token);
    }

    /**
     * Return true if $user and current user is the same
     *
     * @param User $user
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function canAddMe(User $user, TokenInterface $token) : bool
    {
        return $this->isHimself($user, $token);
    }

    /**
     * Return true if $user and current user is the same
     *
     * @param User $user
     * @param TokenInterface $token
     *
     * @return bool
     */
    private function isHimself(User $user, TokenInterface $token) : bool
    {
        $currentUser = $this->getUserFromToken($token);

        if (is_null($currentUser)) {
            return false;
        }

        return $user->getId() === $currentUser->getId();
    }
}
