<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
* @ORM\Entity
* @ORM\Table(name="comment")
* @ORM\Entity(repositoryClass="AppBundle\Repository\CommentRepository")
*/
class Comment
{
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
        $this->setDateTimeRegistration(new \DateTime);
    }

    /**
    * @ORM\Id
    * @ORM\GeneratedValue(strategy="AUTO")
    * @ORM\Column(type="integer", unique=true)
    */
    private $id;

    /**
    * @Assert\NotBlank
    * @ORM\Column(type="text")
    */
    private $content;

    /**
    * @ORM\Column(type="datetime")
    */
    private $dateTimeRegistration;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="id")
     * @ORM\JoinColumn(name="id_user", referencedColumnName="id")
     */
    private $idUser;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Message", inversedBy="id")
     * @ORM\JoinColumn(name="id_message", referencedColumnName="id")
     */
    private $idMessage;

    /**
    * @return mixed
    */
    public function getId()
    {
        return $this->id;
    }

    /**
    * @param mixed $id
    */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
    * @return mixed
    */
    public function getContent()
    {
        return $this->content;
    }

    /**
    * @param mixed $content
    */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
    * @return mixed
    */
    public function getDateTimeRegistration()
    {
        return $this->dateTimeRegistration;
    }

    /**
    * @param mixed $dateTimeRegistration
    */
    public function setDateTimeRegistration($dateTimeRegistration)
    {
        $this->dateTimeRegistration = $dateTimeRegistration;
    }

    /**
    * @return mixed
    */
    public function getIdUser()
    {
        return $this->idUser;
    }

    /**
    * @param mixed User $user
    */
    public function setIdUser(User $user)
    {
        $this->idUser = $user; // Lit le commentaire à l'id du membre
    }

    /**
     * @return mixed
     */
    public function getIdMessage()
    {
        return $this->idMessage;
    }

    /**
     * @param mixed Message $message
     */
    public function setIdMessage(Message $message)
    {
        $this->idMessage = $message;
    }
}