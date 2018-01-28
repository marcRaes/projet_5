<?php

namespace AppBundle\Services;

use AppBundle\Entity\User;

class SendMailer
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig_Environment)
    {
        $this->mailer = $mailer;
        $this->twig = $twig_Environment;
    }

    public function sendRegisterMail(User $user)
    {
            $message = (new \Swift_Message())
                ->setSubject('Confirmation création de compte SOS Harcel')
                ->setFrom('sosharcelnepasrepondre@m-raes.fr')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->twig->render('email/registration.html.twig',
                        array('name' => $user->getPseudo())
                    ),
                    'text/html');

            $this->mailer->send($message);
    }

    public function sendCommentMail($comment)
    {
        if($comment !== null)
        {
            $message = (new \Swift_Message())
                ->setSubject('Votre message contient de nouveau commentaire')
                ->setFrom('sosharcelnepasrepondre@m-raes.fr')
                ->setTo($comment['mailUserMessage'])
                ->setBody(
                    $this->twig->render(
                        'email/comment.html.twig',
                        array('mailComment' => $comment)
                    ),
                    'text/html');

            $this->mailer->send($message);
        }
    }
}