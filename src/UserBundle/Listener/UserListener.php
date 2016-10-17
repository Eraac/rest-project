<?php

namespace UserBundle\Listener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use UserBundle\Entity\User;

class UserListener
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var TokenGeneratorInterface
     */
    private $tokenGenerator;


    public function __construct(MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator)
    {
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function prePersistHandler(User $user, LifecycleEventArgs $event)
    {
        $user->setEnabled(true);
        $user->setConfirmed(false);
        $user->setConfirmationToken($this->tokenGenerator->generateToken());

        $this->mailer->sendConfirmationEmailMessage($user);
    }
}
