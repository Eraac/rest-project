<?php

namespace CoreBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ExtendedViewResponseListener
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorization;


    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorization = $authorizationChecker;
    }

    /**
     * Dynamically add 'admin' group for ROLE_ADMIN
     *
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if ($this->authorization->isGranted('ROLE_ADMIN')) {
            $viewAttribute = $event->getRequest()->attributes->get('_template');

            if (!is_null($viewAttribute)) {

                $groups = $viewAttribute->getSerializerGroups();

                $groups[] = 'admin';

                $viewAttribute->setSerializerGroups($groups);
            }
        }
    }
}
