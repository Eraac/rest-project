<?php

namespace UserBundle\Mailer;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Mailer\MailerInterface;

class Mailer implements MailerInterface
{
    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * @var array
     */
    protected $parameters;


    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig, array $parameters)
    {
        $this->mailer = $mailer;
        $this->twig   = $twig;
        $this->parameters = $parameters;
    }

    public function sendConfirmationEmailMessage(UserInterface $user)
    {
        $template = "UserBundle:Registration:email.txt.twig";

        $context = [
            'user'  => $user,
            'confirmation_url' => $this->parameters['base_url_confirmation'] . $user->getConfirmationToken(),
        ];

        $this->sendMessage($template, $context, $user->getEmail());
    }

    public function sendResettingEmailMessage(UserInterface $user)
    {
        $template = "UserBundle:Resetting:email.txt.twig";

        $context = [
            'user'  => $user,
            'token' => $user->getConfirmationToken(),
        ];

        $this->sendMessage($template, $context, $user->getEmail());
    }

    /**
     * @param string $templateName
     * @param array  $context
     * @param string $toEmail
     */
    protected function sendMessage($templateName, $context, $toEmail)
    {
        $template = $this->twig->loadTemplate($templateName);
        $subject  = $template->renderBlock('subject', $context);
        $textBody = $template->renderBlock('body_text', $context);
        $htmlBody = $template->renderBlock('body_html', $context);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($toEmail);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                ->addPart($textBody, 'text/plain');
        } else {
            $message->setBody($textBody);
        }

        $this->mailer->send($message);
    }
}
