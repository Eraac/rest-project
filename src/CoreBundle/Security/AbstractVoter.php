<?php

namespace CoreBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter as BaseVoter;
use UserBundle\Entity\User;

abstract class AbstractVoter extends BaseVoter
{
    const CREATE    = 'create';
    const VIEW      = 'view';
    const EDIT      = 'edit';
    const DELETE    = 'delete';

    /**
     * @var AccessDecisionManagerInterface
     */
    private $decisionManager;


    /**
     * AbstractVoter constructor.
     *
     * @param AccessDecisionManagerInterface $decisionManager
     */
    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     *
     * @return bool
     */
    protected function supports($attribute, $subject) : bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::VIEW, self::CREATE, self::EDIT, self::DELETE])) {
            return false;
        }

        return true;
    }

    /**
     * @param string $access
     * @param mixed $entity
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function voteOnAttribute($access, $entity, TokenInterface $token) : bool
    {
        // ROLE_ADMIN can do anything
        if ($this->isAdmin($token)) {
            return true;
        }

        $method = 'can' . ucfirst($access);

        assert(method_exists($this, $method), new \LogicException('This code should not be reached!'));

        return $this->$method($entity, $token);
    }

    /**
     * @param TokenInterface $token
     * @param array $role
     *
     * @return bool
     */
    protected function isGranted(TokenInterface $token, array $roles) : bool
    {
        return $this->decisionManager->decide($token, $roles);
    }

    /**
     * Return true if the user is an admin
     *
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function isAdmin(TokenInterface $token) : bool
    {
        return $this->isGranted($token, ['ROLE_ADMIN']);
    }

    /**
     * Return true if the user is connected
     *
     * @param TokenInterface $token
     *
     * @return bool
     */
    protected function isConnected(TokenInterface $token) : bool
    {
        return $this->isGranted($token, ['IS_AUTHENTICATED_FULLY']);
    }

    /**
     * @param TokenInterface $token
     *
     * @return null|User
     */
    protected function getUserFromToken(TokenInterface $token)
    {
        $user = $token->getUser();

        return ($user instanceof User) ? $user : null;
    }
}
