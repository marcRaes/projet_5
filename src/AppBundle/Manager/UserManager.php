<?php

namespace AppBundle\Manager;

class UserManager extends Manager
{
    public function registerUser($user)
    {
        // Persiste le nouveau membre
        $this->em->persist($user);

        // Enregistre le nouveau membre
        $this->em->flush();

        // Message flash pour la bienvenue du nouveau membre
        $this->sessionBag->getFlashBag()->add('success', 'Bienvenue sur votre espace de liberté '.$user->getPseudo());
    }
}