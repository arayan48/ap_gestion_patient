<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chambres', name: 'app_chambre_')]
class ChambreController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('chambre/index.html.twig');
    }
}
