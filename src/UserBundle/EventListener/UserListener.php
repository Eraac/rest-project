<?php

namespace UserBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use UserBundle\Entity\User;
use UserBundle\Mailer\Mailer;

class UserListener
{
    /**
     * @var ContainerInterface
     */
    private $container;


    //public function __construct(MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator)
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function prePersistHandler(User $user, LifecycleEventArgs $event)
    {
        /** @var TokenGeneratorInterface $tokenGenerator */
        $tokenGenerator = $this->container->get('fos_user.util.token_generator');

        /** @var Mailer $mailer */
        $mailer = $this->container->get('user.mailer');

        $user->setEnabled(true);
        $user->setConfirmed(false);
        $user->setConfirmationToken($tokenGenerator->generateToken());

        $mailer->sendConfirmationEmailMessage($user);
    }
}
