<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/patients', name: 'app_patient_')]
class PatientController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('patient/index.html.twig');
    }
}
