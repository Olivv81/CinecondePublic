<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PWType;
use App\Form\PasswordType;

use App\Form\RegistrationType;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\Types\This;
use Doctrine\Persistence\ManagerRegistry;
use App\Notifier\CustomLoginLinkNotification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;


class SecurityController extends AbstractController
{
    /**
     * @route("/inscription", name="security_registration")
     */
    public function registration(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $manager = $doctrine->getManager();
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $hash = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute("security_login");
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
            'active_tab' => ""
        ]);
    }


    /**
     * @Route("/login_check", name="login_check")
     */
    public function check()
    {
        throw new \LogicException('This code should never be reached');
    }

    /**
     * @Route("/admin/sendpass/{email}", name="sendpass")
     */
    public function requestLoginLink(NotifierInterface $notifier, LoginLinkHandlerInterface $loginLinkHandler, ManagerRegistry $doctrine, $email)
    {
        // load the user in some way (e.g. using the form input)
        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => $email]);

        // create a login link for $user this returns an instance
        // of LoginLinkDetails
        $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
        $loginLink = $loginLinkDetails->getUrl();
        // ... send the link and return a response (see next section)
        $notification = new CustomLoginLinkNotification(
            $loginLinkDetails,
            'Bienvenu sur notre site' // email subject
        );
        // create a recipient for this user
        $recipient = new Recipient($user->getEmail());

        // send the notification to the user
        $notifier->send($notification, $recipient);

        date_default_timezone_set('Europe/Paris');

        $today = date_create_from_format('d-m-Y H:i:s', date('d-m-Y H:i:s'));


        $manager = $doctrine->getManager();

        $user->setDateLien($today);
        $manager->persist($user);
        $manager->flush();

        // render a "Login link is sent!" page
        return $this->redirectToRoute('benevoles');
    }

    /**
     * @Route("/profil/password", name="password")
     */
    public function editPassword(ManagerRegistry $doctrine, Request $request, UserPasswordHasherInterface $passwordHasher)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $manager = $doctrine->getManager();
        $user = $this->getUser();
        $form = $this->createForm(PWType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $hash = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hash);
            $manager->persist($user);
            $manager->flush();
            return $this->redirectToRoute('profil');
        }
        return $this->render('security/passwordtype.html.twig', [
            'formPW' => $form->createView(),
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'active_tab' => '',
        ]);
    }

    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
        return $this->redirectToRoute('prog');
    }
}
