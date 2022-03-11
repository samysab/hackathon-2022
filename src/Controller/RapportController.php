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

    #[Route('/admin/uploadFileName', name: 'app_upload_fileName')]
    public function uploadfileName(Request $request, EntityManagerInterface $entityManager): Response
    {
        $upload = new Upload();
        $form = $this->createForm(UploadType::class, $upload);

        $form->handleRequest($request);
        $base64 = $request->request->get('dataDoc');
        $fileContent = $base64;
        $target_dir = "/pdf";
        $decoded_file = base64_decode($base64);
        $mime_type = finfo_buffer(finfo_open(), $decoded_file, FILEINFO_MIME_TYPE); // extract mime type
        $file = md5(uniqid()).'.pdf';
        $file_dir = $target_dir. uniqid().'.pdf';
        file_put_contents($target_dir, $file);

        return new JsonResponse($request->request->get('dataDoc'));
    }
}
