<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @UniqueEntity(groups={"Registration"}, fields="pseudo", message="Ce pseudo n'est pas disponible.")
 * @UniqueEntity(groups={"Registration"}, fields="email", message="Un membre est déjà enregistré avec cette adresse email.")
 */
class User implements UserInterface, \Serializable
{
    public function __construct()
    {
        date_default_timezone_set('Europe/Paris');
        $this->setDateTimeRegistration(new \DateTime);
    }

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Message", mappedBy="idUser", targetEntity="AppBundle\Entity\Comment", mappedBy="idUser")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer", unique=true)
     */
    private $id;

    /**
     * @Assert\NotBlank(groups={"Registration"})
     * @Assert\Length(
     *     groups={"Registration"},
     *     min=5, minMessage="Un pseudo de plus de {{ limit }} caractères est requis.",
     *     max=18, maxMessage="Votre pseudo ne peut dépasser {{ limit }} caractères.")
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9_@-]+$/",
     *     htmlPattern="^[a-zA-Z0-9_@-]+$",
     *     message="Ce pseudo n'est pas valide."
     * )
     * @ORM\Column(type="string", unique=true)
     */
    private $pseudo;

    /**
     * @Assert\NotBlank(groups={"Registration"})
     * @Assert\Email(
     *     message="L'adresse email {{ value }} n'est pas une adresse email valide.",
     *     checkMX=true
     * )
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min=6, minMessage="Le mot de passe doit contenir au moins {{ limit }} caractères.")
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateTimeRegistration;

    /**
     * A non-persisted field that's used to create the encoded password.
     *
     * @var string
     */
    private $plainPassword;

    /**
     * A non-persisted field.
     *
     * @var boolean
     */
    private $memory;

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
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * @param mixed $pseudo
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = strtolower($pseudo);
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
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
     * @return string
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        $this->password = null;
    }

    /**
     * @return bool
     */
    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * @param $memory
     */
    public function setMemory($memory)
    {
        $this->memory = $memory;
    }

    /**
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     */
    public function isPassword(ExecutionContextInterface $context)
    {
        if($this->getPassword() === $this->getPseudo())
        {
            $context
                ->buildViolation('Votre mot de passe ne peut pas être identique au pseudo')
                ->atPath('password')
                ->addViolation();
        }
    }

    /**
     * @Assert\Callback
     * @param ExecutionContextInterface $context
     */
    public function isPseudo(ExecutionContextInterface $context)
    {
        if($this->getPseudo() === 'azerty')
        {
            $context
                ->buildViolation('azerty n\'est pas autorisé.')
                ->atPath('pseudo')
                ->addViolation();
        }
    }

    public function getUsername()
    {
        return $this->pseudo;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->pseudo,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt,
        ));
    }

    /** @see \Serializable::unserialize()
     * @param $serialized
     */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->pseudo,
            $this->email,
            $this->password,
            // see section on salt below
            // $this->salt
            ) = unserialize($serialized);
    }
}