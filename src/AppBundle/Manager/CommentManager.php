<?php

namespace AppBundle\Manager;

use AppBundle\Form\CommentType;

class CommentManager extends Manager
{
    public $formTab = [];

    public function createFormComment($nbMessages)
    {
        for($i=1; $i <= $nbMessages; $i++)
        {
            $this->formTab[] = $this->formFactory->create(CommentType::class)->createView(); // Crée le champ d'ajout d'un commentaire
        }
        return $this->formTab;
    }

    public function saveComment($form, $idMessage)
    {
        $message = $this->em->getRepository('AppBundle:Message')->find($idMessage);
        $user = $this->tokenStorage->getToken()->getUser(); // Récupére l'utilisateur en cours
        $comment = $form->getData(); // Récupére les données du formulaire
        $comment->setIdUser($user); // Enregistre l'id de l'utilisateur qui poste le commentaire
        $comment->setIdMessage($message); // Enregistre l'id du message
        $this->em->persist($comment); // Persiste le commentaire
        $this->em->flush(); // Enregistre le commentaire dans la BDD

        $this->sessionBag->getFlashBag()->add('success', 'Votre commentaire a été posté avec succès !');

        // Si le membre ayant poster le message est différent du membre ayant poster le commentaire
        if($message->getIdUser()->getPseudo() !== $user->getPseudo())
        {
            $mailComment['mailUserMessage'] = $message->getIdUser()->getEmail();
            $mailComment['pseudoUserMessage'] = $message->getIdUser()->getPseudo();
            $mailComment['pseudoUserComment'] = $user->getPseudo();
            $mailComment['contentComment'] = $comment->getContent();
            return $mailComment;
        }
    }
}