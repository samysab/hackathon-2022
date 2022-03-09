<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RapportController extends AbstractController
{
    #[Route('/admin/creation-rapport', name: 'app_rapport')]
    public function index(): Response
    {
        return $this->render('Back/rapport/index.html.twig', [
            'controller_name' => 'RapportController',
        ]);
    }
}
