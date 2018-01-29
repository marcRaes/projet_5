<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Message;
use AppBundle\Entity\User;
use AppBundle\Form\CommentType;
use AppBundle\Form\MessageType;
use AppBundle\Form\LoginUserType;
use AppBundle\Form\RegistrationUserType;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param Request $request
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request) // Méthode page membre
    {
        // Récupére la liste des messages
        $listCommentMessage = $this->getDoctrine()->getManager()->getRepository(Message::class)->getMessagesAll();

        // Crée le champ d'ajout d'un message
        $formAddMessage = $this->createForm(MessageType::class);

        // Crée autant de vue du formulaire d'ajout de commentaire qu'il y'a de messages
        $formTab = [];
        for($i=1; $i <= count($listCommentMessage); $i++)
        {
            $formAddComment = $this->createForm(CommentType::class);
            $formTab[] = $formAddComment->createView();
        }

        // Vérifie la validité du message
        if($request->isMethod('POST') && $formAddMessage->handleRequest($request)->isValid())
        {
            // Sauvegarde le message
            $this->get('app.doctrine.manager')->messageManager()->saveMessage($formAddMessage);

            return $this->redirectToRoute('homepage');
        }
        else if($request->isMethod('POST') && $formAddComment->handleRequest($request)->isValid()) // Un membre souhaite poster un commentaire
        {
            // Sauvegarde du commentaire
            $mailComment = $this->get('app.doctrine.manager')->commentManager()->saveComment($formAddComment, intval($request->get('idMessage')));

            // Envoie d'un mail au membre ayant poster le message
            $this->get('app.send_mail')->sendCommentMail($mailComment);

            return $this->redirectToRoute('homepage');
        }

        return $this->render('page/homepage.html.twig', array(
            'formAddMessage' => $formAddMessage->createView(),
            'formAddComment' => $formTab,
            'listCommentMessageUser' => $listCommentMessage,
        ));
    }

    /**
     * @Route("/{id}", name="pageUser", requirements={"id"="\d+"})
     * @Security("is_granted('IS_AUTHENTICATED_REMEMBERED')")
     * @ParamConverter("id", class="AppBundle\Entity\User")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function messageUserAction($id)
    {
        $listCommentMessageUser = $this->getDoctrine()->getManager()->getRepository(Message::class)->getMessagesUser($id);

        // Appel de la vue
        return $this->render('page/pageUser.html.twig', array(
            'listCommentMessageUser' => $listCommentMessageUser,
        ));
    }

    /**
     * @Route("/register", name="register")
     * @param Request $request
     * @return null|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function connectUserAction(Request $request) // Méthode du formulaire d'enregistrement
    {
        // L'utilisateur est déja connecter, il est rediriger vers la page d'accueil
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('homepage');
        }

        $user = new User();
        $form = $this->createForm(RegistrationUserType::class, $user); // Crée le formulaire d'enregistrement

        // Soumission du formulaire
        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            // Tout est OK on enregistre le membre
            $this->get('app.doctrine.manager')->userManager()->registerUser($user);

            // Envoie d'un mail de bienvenue au nouveau membre
            $this->get('app.send_mail')->sendRegisterMail($user);

            // Connecte l'utilisateur nouvellement inscrit
            return $this->get('security.authentication.guard_handler')
                ->authenticateUserAndHandleSuccess($user, $request, $this->get('app.security.login_form_authenticator'), 'main');
        }

        // Appel de la vue
        return $this->render('page/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/login", name="login")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request) // Méthode du formulaire de connexion
    {
        // L'utilisateur est déja connecter, il est rediriger vers la page d'accueil
        if($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED'))
        {
            return $this->redirectToRoute('homepage');
        }

        $form = $this->createForm(LoginUserType::class, new User()); // Crée le formulaire de connexion

        return $this->render('page/connect.html.twig', array(
            'form' => $form->createView(), // Retourne le formulaire à la vue
        ));
    }

    /**
     * @Route("/logout", name="deconnect")
     */
    public function logoutAction()
    {
        return;
    }
}