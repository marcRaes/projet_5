<?php

namespace AppBundle\Security;

use AppBundle\Entity\User;
use AppBundle\Form\LoginUserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    use TargetPathTrait;

    private $formFactory;
    private $em;
    private $router;
    private $passwordEncoder;
    private $sessionBag;

    public function __construct(FormFactoryInterface $formFactory, EntityManagerInterface $em, RouterInterface $router, UserPasswordEncoderInterface $passwordEncoder, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->router = $router;
        $this->passwordEncoder = $passwordEncoder;
        $this->sessionBag = new Session();
    }

    public function getCredentials(Request $request)
    {
        $isLoginSubmit = $request->getPathInfo() == '/login' && $request->isMethod('POST');

        if(!$isLoginSubmit)
        {
            return null;
        }

        $form = $this->formFactory->create(LoginUserType::class);
        $form->handleRequest($request);

        $data = $form->getData();

        $request->getSession()->set(
            Security::LAST_USERNAME,
            $data->getPseudo()
        );

        return $data;
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     * @return mixed|null|UserInterface
     * @throws NonUniqueResultException
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $userBdd = $this->em->getRepository(User::class)->loadUserByUsername($credentials->getPseudo());

        return $userBdd;
    }

    public function checkCredentials($credentials, UserInterface $user)
    {
        if($this->passwordEncoder->isPasswordValid($user, $credentials->getPassword()))
        {
            // Message flash souhaitant la bienvenue au membre connecter
            $this->sessionBag->getFlashBag()->add('success', 'Ravis de vous revoir '.$user->getUsername());
            return true;
        }
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $targetPath = $this->getTargetPath($request->getSession(), $providerKey);

        if(!$targetPath)
        {
            $targetPath = $this->router->generate('homepage'); // Redirection vers l'accueil du site
        }

        return new RedirectResponse($targetPath);
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $this->sessionBag->getFlashBag()->add('error', 'Vos données de connexion n\'ont pas était reconnu'); // Message d'erreur
    }

    protected function getLoginUrl()
    {
        return $this->router->generate('register');
    }
}