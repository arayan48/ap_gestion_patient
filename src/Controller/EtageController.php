<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/etages', name: 'app_etage_')]
class EtageController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(): Response
    {
        return $this->render('etage/index.html.twig');
    }
}
