<?php
namespace AppBundle\Manager;

class MessageManager extends Manager
{
    public function saveMessage($form)
    {
        // Récupére l'utilisateur en cours
        $user = $this->tokenStorage->getToken()->getUser();

        // Récupére les données du formulaire
        $message = $form->getData();

        // Enregistre l'id de l'utilisateur qui poste le message
        $message->setIdUser($user);

        // Persiste le message
        $this->em->persist($message);

        // Enregistre le message dans la BDD
        $this->em->flush();

        // Message flash signalant que le message à était poster avec succés
        $this->sessionBag->getFlashBag()->add('success', 'Votre message a été posté avec succès !');
    }
}