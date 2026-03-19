<?php

namespace App\Controller;

use App\Repository\ChambreRepository;
use App\Repository\EmployeRepository;
use App\Repository\PatientRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        PatientRepository $patientRepo,
        ChambreRepository $chambreRepo,
        ReservationRepository $reservationRepo,
        EmployeRepository $employeRepo,
    ): Response {
        $stats = null;
        if ($this->getUser()) {
            $stats = [
                'patients'     => $patientRepo->count([]),
                'reservations' => $reservationRepo->count([]),
            ];
            if ($this->isGranted('ROLE_ADMINISTRATEUR')) {
                $stats['chambres'] = $chambreRepo->count([]);
                $stats['employes'] = $employeRepo->count([]);
            }
        }

        return $this->render('home/index.html.twig', ['stats' => $stats]);
    }
}
