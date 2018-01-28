<?php
namespace AppBundle\Manager;

class MessageManager extends Manager
{
    public function saveMessage($form)
    {
        $user = $this->tokenStorage->getToken()->getUser(); // Récupére l'utilisateur en cours
        $message = $form->getData(); // Récupére les données du formulaire
        $message->setIdUser($user); // Enregistre l'id de l'utilisateur qui poste le message
        $this->em->persist($message); // Persiste le message
        $this->em->flush(); // Enregistre le message dans la BDD

        $this->sessionBag->getFlashBag()->add('success', 'Votre message a été posté avec succès !');
    }
}