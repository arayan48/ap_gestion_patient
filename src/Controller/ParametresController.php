<?php

namespace App\Controller;

use App\Repository\ChambreRepository;
use App\Repository\EmployeRepository;
use App\Repository\LitRepository;
use App\Repository\LogRepository;
use App\Repository\PatientRepository;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/parametres', name: 'app_parametres')]
class ParametresController extends AbstractController
{
    #[Route('', name: '', methods: ['GET'])]
    public function index(
        Request $request,
        PatientRepository $patientRepo,
        ChambreRepository $chambreRepo,
        LitRepository $litRepo,
        ReservationRepository $reservationRepo,
        EmployeRepository $employeRepo,
        LogRepository $logRepo,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_ADMINISTRATEUR');

        // Stats globales
        $stats = [
            'patients'     => $patientRepo->count([]),
            'chambres'     => $chambreRepo->count([]),
            'lits'         => $litRepo->count([]),
            'reservations' => $reservationRepo->count([]),
            'employes'     => $employeRepo->count([]),
            'logs'         => $logRepo->count([]),
        ];

        // Réservations par statut
        $statsReservations = [
            'en_cours'  => $reservationRepo->count(['statut' => 'en_cours']),
            'terminee'  => $reservationRepo->count(['statut' => 'terminee']),
            'annulee'   => $reservationRepo->count(['statut' => 'annulee']),
        ];

        // Journal d'audit — 50 entrées les plus récentes
        $page    = max(1, (int) $request->query->get('page', 1));
        $limit   = 25;
        $offset  = ($page - 1) * $limit;
        $total   = $logRepo->count([]);
        $pages   = (int) ceil($total / $limit);

        $logs = $logRepo->findBy([], ['dateAction' => 'DESC'], $limit, $offset);

        return $this->render('parametres/index.html.twig', [
            'stats'              => $stats,
            'statsReservations'  => $statsReservations,
            'logs'               => $logs,
            'page'               => $page,
            'pages'              => $pages,
            'total'              => $total,
        ]);
    }
}
