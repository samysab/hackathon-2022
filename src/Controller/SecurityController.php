<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


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

    #[Route(path: '/generateLogin', name: 'app_generateLogin')]
    public function generateLogin(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher){

        $user = new User();

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
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
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
