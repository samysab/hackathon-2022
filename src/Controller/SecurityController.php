<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Form\GenerateLoginType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Service\apiMailerService;


class SecurityController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['GET','POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(["ROLE_USER"]);
            dd($passwordHasher->hashPassword($user, $user->getPassword()));
            $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_front_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('security/register.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    function generate_string($input, $strength = 16) {
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    #[Route(path: '/admin/finish-rapport', name: 'app_generateLogin', methods: ['GET','POST'])]
    public function generateLogin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer){
        $user = new User();

        $form = $this->createForm(GenerateLoginType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@&-_';
            $password = $this->generate_string($permitted_chars, 10);
            $user->setPassword($passwordHasher->hashPassword($user,$password ));
            $user->setRoles(["ROLE_USER"]);
            $entityManager->persist($user);
            $entityManager->flush();

            $email = (new Email())
                ->from('wbhackathon2022@example.com')
                ->to("samy.sab92@gmail.com")
                ->subject("[WB] Votre rapportOld d'étude est prêt !")
                ->text('Sending emails is fun again!')
                ->html("<h2>Votre rapportOld d'étude est enfin prêt !</h2><p>Nous vous avons créer un login et un mot de passe afin que vous puissiez acceder à votre espace client</p><p><u>Information de connexion :</u></p><p>Login : ".$user->getLogin()."</p><p>Email : ".$user->getEmail()."</p><p>Mot de passe : ".$password. "</p>");

            $mailer->send($email);
            return $this->redirectToRoute('app_back_home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('security/generateLogin.html.twig', [
            'user' => $user,
            'formInvitation' => $form,
        ]);
    }


    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout()
    {
        return $this->redirectToRoute('app_front_home', [], Response::HTTP_SEE_OTHER);
    }

    #[Route(path: '/admin/home', name: 'app_back_home')]
    public function displayBack(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('Back/home.html.twig');
    }
    #[Route(path: '/', name: 'app_front_home')]
    public function displayFront(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('Front/home.html.twig');
    }
}
