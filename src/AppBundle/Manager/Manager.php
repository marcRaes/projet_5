<?php

namespace AppBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\MailHandler;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class Manager
{
    protected $em;
    protected $tokenStorage;
    protected $formFactory;
    protected $sessionBag;

    public function __construct(EntityManagerInterface $entityManager, TokenStorage $tokenStorage, FormFactory $formFactory)
    {
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->formFactory = $formFactory;
        $this->sessionBag = new Session();
    }

    public function userManager()
    {
        return new UserManager($this->em, $this->tokenStorage, $this->formFactory, $this->sessionBag);
    }

    public function messageManager()
    {
        return new MessageManager($this->em, $this->tokenStorage, $this->formFactory, $this->sessionBag);
    }

    public function commentManager()
    {
        return new CommentManager($this->em, $this->tokenStorage, $this->formFactory, $this->sessionBag);
    }
}