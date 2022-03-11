<?php

namespace App\Controller;

use App\Entity\Rapport;
use App\Entity\Upload;
use App\Entity\User;
use App\Form\UploadType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\RapportType;
use App\Repository\RapportRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RapportController extends AbstractController
{
    function generate_string($input, $strength = 16) {
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }
    #[Route('/admin/creation-rapport', name: 'app_rapport')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, MailerInterface $mailer, UserRepository $userRepository): Response
    {
        $upload = new Upload();
        $form = $this->createForm(UploadType::class, $upload);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $file = $upload->getName();
            $filename = md5(uniqid()).'.pdf';
            $file->move($this->getParameter('upload_directory'), $filename);
            $upload->setName($filename);

            $entityManager->persist($upload);
            $entityManager->flush();


            $user = new User();

            $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ@&-_';
            $password = $this->generate_string($permitted_chars, 10);
            $user->setEmail("samy.sab92@gmail.com");
            $user->setPassword($passwordHasher->hashPassword($user,$password ));
            $user->setRoles(["ROLE_USER"]);
            $entityManager->persist($user);
            $entityManager->flush();

            $rapport = new Rapport();
            $rapport->setPrice(0);

            $rapport->setUserId($user);
            $rapport->setUpload($upload);

            $entityManager->persist($rapport);
            $entityManager->flush();

            $email = (new Email())
                ->from('wbhackathon2022@example.com')
                ->to("samy.sab92@gmail.com")
                ->subject("[WB] Votre rapportOld d'étude est prêt !")
                ->text('Sending emails is fun again!')
                ->html("<h2>Votre rapportOld d'étude est enfin prêt !</h2><p>Nous vous avons créer un login et un mot de passe afin que vous puissiez acceder à votre espace client</p><p><u>Information de connexion :</u></p><p>Login : ".$user->getLogin()."</p><p>Email : ".$user->getEmail()."</p><p>Mot de passe : ".$password. "</p>");

            $mailer->send($email);

            return $this->redirectToRoute("app_back_home");
        }

        return $this->render('Back/rapport/index.html.twig', [
            'controller_name' => 'RapportController',
            'formUpload' => $form->createView()
        ]);
    }


    #[Route('/admin/uploadFile', name: 'app_upload_file')]
    public function uploadfile(Request $request, EntityManagerInterface $entityManager): Response
    {
    $upload = new Upload();
    $form = $this->createForm(UploadType::class, $upload);

    $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $base64 = base64_decode($request->request->get('dataDoc'));

            $file = $upload->getName();
            $filename = md5(uniqid()).'.pdf';
            $file->move($this->getParameter('upload_directory'), $filename);
            $upload->setName($filename);

            $entityManager->persist($upload);
            $entityManager->flush();



            return new JsonResponse($request->request->get('dataDoc'));
       }

        return $this->render('base.html.twig', ['formUpload' => $form->createView()]);
    }
    #[Route('/admin/creation-rapport', name: 'app_rapport_creation')]
    public function creationRapport(): Response
    {

        return $this->render('Back/rapport/index.html.twig', [
            'controller_name' => 'RapportController',
        ]);
    }

    #[Route('/admin/rapport/{id}', name: 'app_rapport_show', methods: ['GET'])]
    public function show(Rapport $rapport): Response
    {
        return $this->render('Back/rapport/show.html.twig', [
            'rapport' => $rapport,
        ]);
    }

    #[Route('/admin/rapport/{id}/edit', name: 'app_rapport_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RapportType::class, $rapport);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rapport_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('rapport/edit.html.twig', [
            'rapport' => $rapport,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rapport_delete', methods: ['POST'])]
    public function delete(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rapport->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rapport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rapport_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/mon-rapport', name: 'app_mon_rapport')]
    public function monRapport(RapportRepository $rapportRepository): Response
    {
        return $this->render('rapport/index.html.twig', [
            'controller_name' => 'RapportController',
            'rapports' => $rapportRepository->findAll()
        ]);
    }
}
