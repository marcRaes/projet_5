<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Message;
use Doctrine\ORM\EntityRepository;

class MessageRepository extends EntityRepository
{
    public function getMessagesAll()
    {
        $messages = $this->createQueryBuilder(Message::class)
            ->select('m, u')
            ->from('AppBundle:Message', 'm')
            ->join('m.idUser', 'u')
            ->orderBy('m.dateTimeRegistration', 'DESC')
            ->getQuery()->getArrayResult();

        return $this->_em->getRepository(Comment::class)->GetCommentsMessage($messages);
    }

    public function getMessagesUser($id)
    {
        $messages = $this->createQueryBuilder(Message::class)
            ->select('m, u')
            ->from('AppBundle:Message', 'm')
            ->join('m.idUser', 'u')
            ->orderBy('m.dateTimeRegistration', 'DESC')
            ->where('m.idUser = :id')
            ->setParameter('id', $id)
            ->getQuery()->getArrayResult();

        return $this->_em->getRepository(Comment::class)->GetCommentsMessageUser($messages, $id);
    }
}