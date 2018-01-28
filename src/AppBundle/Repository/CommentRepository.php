<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class CommentRepository extends EntityRepository
{
    public function GetCommentsMessage($messages)
    {
        $comments = [];

        for($i=0; $i < count($messages); $i++)
        {
            $idMessage = $messages[$i]['id'];

            $comments[] = $this->findBy(
                array('idMessage' => $idMessage)
            );

            $messages[$i]['comment'] = $comments[$i];
        }

        return $messages;
    }

    public function GetCommentsMessageUser($messages, $id)
    {
        $comments = [];

        for($i=0; $i < count($messages); $i++)
        {
            $idMessage = $messages[$i]['id'];

            $comments[] = $this->findBy(
                array('idMessage' => $idMessage, 'idUser' => $id)
            );

            $messages[$i]['comment'] = $comments[$i];
        }

        return $messages;
    }
}