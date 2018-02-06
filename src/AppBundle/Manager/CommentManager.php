<?php

namespace AppBundle\Manager;

class CommentManager extends Manager
{
    public function saveComment($form, $idMessage)
    {
        // Récupére le message ou le commentaire est poster
        $message = $this->em->getRepository('AppBundle:Message')->find($idMessage);

        // Récupére l'utilisateur en cours
        $user = $this->tokenStorage->getToken()->getUser();

        // Récupére les données du formulaire
        $comment = $form->getData();

        // Enregistre l'id de l'utilisateur qui poste le commentaire
        $comment->setIdUser($user);

        // Enregistre l'id du message
        $comment->setIdMessage($message);

        // Persiste le commentaire
        $this->em->persist($comment);

        // Enregistre le commentaire dans la BDD
        $this->em->flush();

        // Message flash signalant que le comentaire a été poster avec succès
        $this->sessionBag->getFlashBag()->add('success', 'Votre commentaire a été posté avec succès !');

        // Si le membre ayant poster le message est différent du membre ayant poster le commentaire
        if($message->getIdUser()->getPseudo() !== $user->getPseudo())
        {
            // Récupére l'email du membre ayant poster le message
            $mailComment['mailUserMessage'] = $message->getIdUser()->getEmail();
            // Récupére le pseudo du membre ayant poster le message
            $mailComment['pseudoUserMessage'] = $message->getIdUser()->getPseudo();
            // Récupére le pseudo du membre ayant poster le commentaire
            $mailComment['pseudoUserComment'] = $user->getPseudo();
            // Récupére le contenu du commentaire
            $mailComment['contentComment'] = $comment->getContent();

            return $mailComment;
        }
    }
}