<?php

namespace CoreBundle\EventListener;

use CoreBundle\Service\SerializerGroupsManager;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ExtendedViewResponseListener
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorization;

    /**
     * @var SerializerGroupsManager
     */
    private $serializerGroupsManager;


    /**
     * ExtendedViewResponseListener constructor.
     *
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param serializerGroupsManager $serializerGroupsManager
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, SerializerGroupsManager $serializerGroupsManager)
    {
        $this->authorization = $authorizationChecker;
        $this->serializerGroupsManager = $serializerGroupsManager;
    }

    /**
     * Dynamically add 'admin' group for ROLE_ADMIN
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if ($this->authorization->isGranted('ROLE_ADMIN')) {
            $this->serializerGroupsManager->addGroup('admin');
        }
    }
}
