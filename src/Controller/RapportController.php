<?php

namespace App\Controller;

use App\Entity\Rapport;
use App\Entity\Upload;
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
use Symfony\Component\Routing\Annotation\Route;

class RapportController extends AbstractController
{
    #[Route('/admin/creation-rapport', name: 'app_rapport')]
    public function index(Request $request,  EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $upload = new Upload();
        $form = $this->createForm(UploadType::class, $upload);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            dump($request->request->get('inputInvitation'));

            $file = $upload->getName();
            $filename = md5(uniqid()).'.pdf';
            $file->move($this->getParameter('upload_directory'), $filename);
            $upload->setName($filename);

            $entityManager->persist($upload);
            $entityManager->flush();

            $rapport = new Rapport();
            $rapport->setPrice(0);

            $user = $userRepository->findBy(array('id' => '1'));
            $rapport->setUserId($user[0]);
            $rapport->setUpload($upload);

            $entityManager->persist($rapport);
            $entityManager->flush();


            return $this->render('Back/rapport/index.html.twig', [
                'controller_name' => 'RapportController',
                'formUpload' => $form->createView()
            ]);
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
