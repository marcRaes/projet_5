<?php

namespace AppBundle\Manager;

class UserManager extends Manager
{
    public function registerUser($user)
    {
        $this->em->persist($user);
        $this->em->flush();

        $this->sessionBag->getFlashBag()->add('success', 'Bienvenue sur votre espace de liberté '.$user->getPseudo());
    }

    public function mailUser($user)
    {
        $message = \Swift_Mailer::newInstance($this->transport)
            ->setSubject('Confirmation création de compte SOS Harcel')
            ->setFrom('sosharcelnepasrepondre@m-raes.fr')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                // app/Resources/views/Emails/registration.html.twig
                    'email/registration.html.twig',
                    array('name' => $user->getPseudo())
                ),
                'text/html');

        $this->mailer->send($message);
    }
}