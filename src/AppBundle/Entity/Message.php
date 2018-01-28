<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="message")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MessageRepository")
 */
class Message
{
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
        $this->setDateTimeRegistration(new \DateTime);
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="idMessage")
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
        $this->idUser = $user; // Lit le message à l'id du membre
    }
}